<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
                    return response()->json(['error' => 'Un respaldo ya está en proceso. Intenta de nuevo en unos segundos.'], 429);
                }
                // Lock stale detectado, permitir continuar
            }
            if (!@file_put_contents($lockFile, time())) {
                return response()->json(['error' => 'No se pudo crear el lock de respaldo. Intenta de nuevo.'], 500);
            }

            // 2) Verificar ventas activas (estado distinto de 'completada' o 'anulada')
            $tieneActivas = Venta::whereNotIn('estado', ['completada', 'anulada'])->exists();
            if ($tieneActivas) {
                @unlink($lockFile);
                return response()->json(['error' => 'Hay ventas en curso. Finalízalas antes de crear la copia de seguridad.'], 400);
            }

            // 2) Ruta origen del archivo SQLite dentro del proyecto
            $source = base_path('database' . DIRECTORY_SEPARATOR . 'database.sqlite');
            if (!file_exists($source)) {
                @unlink($lockFile);
                return response()->json(['error' => 'No se encontró el archivo de base de datos (database/database.sqlite).'], 404);
            }

            // 3) WAL checkpoint: consolidar datos de Write-Ahead Log en archivo principal
            try {
                DB::statement('PRAGMA wal_checkpoint(FULL);');
            } catch (\Exception $walError) {
                // Log pero no fallar: WAL puede no estar activo, es optional
            }

            // 4) Crear directorio temporal si no existe
            $tempDir = storage_path('app' . DIRECTORY_SEPARATOR . 'temp');
            if (!is_dir($tempDir)) {
                if (!@mkdir($tempDir, 0755, true) && !is_dir($tempDir)) {
                    @unlink($lockFile);
                    return response()->json(['error' => 'No se pudo crear el directorio temporal.'], 500);
                }
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
                @unlink($lockFile);
                return response()->json(['error' => 'Error al copiar el archivo de base de datos. Verifica espacio y permisos.'], 500);
            }

            // 7) VALIDACIÓN POST-COPIA: Verificar integridad del backup
            try {
                $backupPDO = new \PDO('sqlite:' . $target);
                $backupPDO->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                
                // Ejecutar integrity check en el backup
                $result = $backupPDO->query('PRAGMA integrity_check;')->fetch(\PDO::FETCH_COLUMN);
                
                if ($result !== 'ok') {
                    // Backup corrupto: eliminarlo
                    @unlink($target);
                    @unlink($lockFile);
                    return response()->json(['error' => 'El respaldo generado está corrupto. Se eliminó. Intenta de nuevo.'], 500);
                }

                // Cerrar conexión al backup
                $backupPDO = null;

            } catch (\Exception $integrityError) {
                // Error al validar (BD corrupta, no accesible, etc.)
                @unlink($target);
                @unlink($lockFile);
                return response()->json(['error' => 'Error validando el respaldo: ' . $integrityError->getMessage()], 500);
            }

            // 8) Limpieza y retornar descarga
            @unlink($lockFile);
            return response()->download($target, $fileName)->deleteFileAfterSend(true);

        } catch (\Throwable $e) {
            if ($lockFile) {
                @unlink($lockFile);
            }
            return response()->json(['error' => 'Error creando respaldo: ' . $e->getMessage()], 500);
        }
    }
}
