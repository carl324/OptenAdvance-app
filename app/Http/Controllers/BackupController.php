<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Venta;

class BackupController extends Controller
{
    /**
     * Crea un backup del archivo SQLite y lo retorna como descarga del navegador.
     * Incluye: WAL checkpoint, validación post-copia, prevención de doble ejecución.
     */
    public function store(Request $request)
    {
        $lockFile = null;

        try {
            // 0) Validar confirmación (checkbox backend)
            if (!$request->input('confirm_backup')) {
                return response()->json(['error' => 'Debes confirmar que deseas crear el respaldo.'], 400);
            }

            // 1) Prevenir doble ejecución: crear lock temporal
            $lockFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'opten_backup.lock';
            $lockTimeout = 5 * 60; // 5 minutos en segundos (Bug #26: stale lock detection)
            
            if (file_exists($lockFile)) {
                $lockTime = (int)@file_get_contents($lockFile);
                $currentTime = time();
                // Si el lock es más antiguo que 5 minutos, asumir que es stale y sobreescribir
                if ($currentTime - $lockTime < $lockTimeout) {
                    Log::warning('BackupController::store - Intento de backup mientras otro está en proceso');
                    return response()->json(['error' => 'Un respaldo ya está en proceso. Intenta de nuevo en unos segundos.'], 429);
                }
                // Lock stale detectado, permitir continuar
                Log::info('BackupController::store - Lock stale detectado y liberado');
            }
            if (!@file_put_contents($lockFile, time())) {
                Log::error('BackupController::store - No se pudo crear archivo lock');
                return response()->json(['error' => 'No se pudo crear el lock de respaldo. Intenta de nuevo.'], 500);
            }

            // 2) Verificar ventas activas (estado distinto de 'completada' o 'anulada')
            $tieneActivas = Venta::whereNotIn('estado', ['completada', 'anulada'])->exists();
            if ($tieneActivas) {
                Log::warning('BackupController::store - Intento de backup con ventas activas en curso');
                @unlink($lockFile);
                return response()->json(['error' => 'Hay ventas en curso. Finalízalas antes de crear la copia de seguridad.'], 400);
            }

            // 2) Ruta origen del archivo SQLite dentro del proyecto
            $source = base_path('database' . DIRECTORY_SEPARATOR . 'database.sqlite');
            if (!file_exists($source)) {
                Log::error('BackupController::store - Archivo database.sqlite no encontrado en: ' . $source);
                @unlink($lockFile);
                return response()->json(['error' => 'No se encontró el archivo de base de datos (database/database.sqlite).'], 404);
            }

            // 3) WAL checkpoint: consolidar datos de Write-Ahead Log en archivo principal
            try {
                DB::statement('PRAGMA wal_checkpoint(FULL);');
                Log::info('BackupController::store - WAL checkpoint completado');
            } catch (\Exception $walError) {
                // Log pero no fallar: WAL puede no estar activo, es optional
                Log::warning('BackupController::store - WAL checkpoint error (no crítico): ' . $walError->getMessage());
            }

            // 4) Crear directorio temporal si no existe
            $tempDir = storage_path('app' . DIRECTORY_SEPARATOR . 'temp');
            if (!is_dir($tempDir)) {
                if (!@mkdir($tempDir, 0755, true) && !is_dir($tempDir)) {
                    Log::error('BackupController::store - No se pudo crear directorio temp: ' . $tempDir);
                    @unlink($lockFile);
                    return response()->json(['error' => 'No se pudo crear el directorio temporal.'], 500);
                }
                Log::info('BackupController::store - Directorio temp creado: ' . $tempDir);
            }

            // 5) Nombre de archivo y evitar sobreescritura
            $timestamp = date('Y-m-d_H-i-s');
            $fileName = "opten_backup_{$timestamp}.sqlite";
            $target = $tempDir . DIRECTORY_SEPARATOR . $fileName;
            if (file_exists($target)) {
                $fileName = "opten_backup_{$timestamp}_" . uniqid() . '.sqlite';
                $target = $tempDir . DIRECTORY_SEPARATOR . $fileName;
            }

            // 6) Copiar el archivo
            if (!@copy($source, $target)) {
                Log::error('BackupController::store - No se pudo copiar database.sqlite. Origen: ' . $source . ' | Destino: ' . $target);
                @unlink($lockFile);
                return response()->json(['error' => 'Error al copiar el archivo de base de datos. Verifica espacio y permisos.'], 500);
            }
            Log::info('BackupController::store - Database copiada a: ' . $target);

            // 7) VALIDACIÓN POST-COPIA: Verificar integridad del backup
            try {
                $backupPDO = new \PDO('sqlite:' . $target);
                $backupPDO->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                
                // Ejecutar integrity check en el backup
                $result = $backupPDO->query('PRAGMA integrity_check;')->fetch(\PDO::FETCH_COLUMN);
                
                if ($result !== 'ok') {
                    // Backup corrupto: eliminarlo
                    Log::error('BackupController::store - Backup corrupto detectado. Integrity check result: ' . $result . ' | Archivo eliminado: ' . $target);
                    @unlink($target);
                    @unlink($lockFile);
                    return response()->json(['error' => 'El respaldo generado está corrupto. Se eliminó. Intenta de nuevo.'], 500);
                }

                Log::info('BackupController::store - Integrity check passed para: ' . $fileName);

                // Cerrar conexión al backup
                $backupPDO = null;

            } catch (\Exception $integrityError) {
                // Error al validar (BD corrupta, no accesible, etc.)
                Log::error('BackupController::store - Error en integrity check: ' . $integrityError->getMessage() . ' | Archivo: ' . $target);
                @unlink($target);
                @unlink($lockFile);
                return response()->json(['error' => 'Error validando el respaldo: ' . $integrityError->getMessage()], 500);
            }

            // 8) Limpieza y retornar descarga
            Log::info('BackupController::store - Backup completado exitosamente: ' . $fileName);
            @unlink($lockFile);
            return response()->download($target, $fileName)->deleteFileAfterSend(true);

        } catch (\Throwable $e) {
            Log::error('BackupController::store - Excepción no capturada: ' . $e->getMessage() . ' | Stack trace: ' . $e->getTraceAsString());
            if ($lockFile) {
                @unlink($lockFile);
            }
            return response()->json(['error' => 'Error creando respaldo: ' . $e->getMessage()], 500);
        }
    }
}
