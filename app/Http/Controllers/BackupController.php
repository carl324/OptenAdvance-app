<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Venta;
use Illuminate\Support\Facades\Config;

class BackupController extends Controller
{
    /**
     * Crea un backup de la base de datos MySQL y lo retorna como descarga del navegador.
     * Incluye: validación, prevención de doble ejecución, dump SQL comprimido.
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
            $lockFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'opten_db.lock';
            $lockTimeout = 5 * 60; // 5 minutos
            
            if (file_exists($lockFile)) {
                $lockTime = (int)@file_get_contents($lockFile);
                $currentTime = time();
                if ($currentTime - $lockTime < $lockTimeout) {
                    Log::warning('BackupController::store - Intento de backup mientras otro está en proceso');
                    return response()->json(['error' => 'Un respaldo ya está en proceso. Intenta de nuevo en unos segundos.'], 429);
                }
                Log::info('BackupController::store - Lock stale detectado y liberado');
            }
            if (!@file_put_contents($lockFile, time())) {
                Log::error('BackupController::store - No se pudo crear archivo lock');
                return response()->json(['error' => 'No se pudo crear el lock de respaldo. Intenta de nuevo.'], 500);
            }

            // 2) Verificar ventas activas
            $tieneActivas = Venta::whereNotIn('estado', ['completada', 'anulada'])->exists();
            if ($tieneActivas) {
                Log::warning('BackupController::store - Intento de backup con ventas activas en curso');
                @unlink($lockFile);
                return response()->json(['error' => 'Hay ventas en curso. Finalízalas antes de crear la copia de seguridad.'], 400);
            }

            // 3) Obtener configuración de la base de datos
            $dbHost = Config::get('database.connections.mysql.host');
            $dbPort = Config::get('database.connections.mysql.port', 3306);
            $dbName = Config::get('database.connections.mysql.database');
            $dbUser = Config::get('database.connections.mysql.username');
            $dbPass = Config::get('database.connections.mysql.password');

            if (empty($dbName)) {
                Log::error('BackupController::store - Configuración de base de datos incompleta');
                @unlink($lockFile);
                return response()->json(['error' => 'Configuración de base de datos no válida.'], 500);
            }

            // 4) Crear directorio temporal
            $tempDir = storage_path('app' . DIRECTORY_SEPARATOR . 'temp');
            if (!is_dir($tempDir)) {
                if (!@mkdir($tempDir, 0755, true) && !is_dir($tempDir)) {
                    Log::error('BackupController::store - No se pudo crear directorio temp: ' . $tempDir);
                    @unlink($lockFile);
                    return response()->json(['error' => 'No se pudo crear el directorio temporal.'], 500);
                }
            }

            // 5) Nombre de archivo
            $timestamp = date('Y-m-d_H-i-s');
            $fileName = "opten_db_{$timestamp}.sql";
            $target = $tempDir . DIRECTORY_SEPARATOR . $fileName;

            // 6) Ejecutar mysqldump
            // Detectar ruta de mysqldump según el sistema operativo
            $mysqldumpPath = $this->detectMysqldumpPath();
            
            if (!$mysqldumpPath) {
                Log::error('BackupController::store - mysqldump no encontrado');
                @unlink($lockFile);
                return response()->json(['error' => 'No se encontró mysqldump. Verifica que MySQL esté instalado correctamente.'], 500);
            }

            // Construir comando mysqldump
            $command = sprintf(
                '"%s" --host=%s --port=%s --user=%s --password=%s --single-transaction --routines --triggers %s > "%s" 2>&1',
                $mysqldumpPath,
                escapeshellarg($dbHost),
                escapeshellarg($dbPort),
                escapeshellarg($dbUser),
                escapeshellarg($dbPass),
                escapeshellarg($dbName),
                $target
            );

            // Ejecutar comando
            $output = [];
            $returnVar = 0;
            exec($command, $output, $returnVar);

            if ($returnVar !== 0) {
                $errorMsg = implode("\n", $output);
                Log::error('BackupController::store - Error en mysqldump: ' . $errorMsg);
                @unlink($target);
                @unlink($lockFile);
                return response()->json(['error' => 'Error al crear el respaldo de MySQL. Revisa los logs.'], 500);
            }

            // 7) Validar que el archivo se creó y tiene contenido
            if (!file_exists($target) || filesize($target) < 100) {
                Log::error('BackupController::store - Archivo de backup vacío o no creado: ' . $target);
                @unlink($target);
                @unlink($lockFile);
                return response()->json(['error' => 'El archivo de respaldo está vacío o no se generó correctamente.'], 500);
            }

            Log::info('BackupController::store - Backup MySQL completado exitosamente: ' . $fileName . ' (' . filesize($target) . ' bytes)');

            // 8) Limpieza y retornar descarga
            @unlink($lockFile);
            return response()->download($target, $fileName)->deleteFileAfterSend(true);

        } catch (\Throwable $e) {
            Log::error('BackupController::store - Excepción: ' . $e->getMessage());
            if ($lockFile && file_exists($lockFile)) {
                @unlink($lockFile);
            }
            return response()->json(['error' => 'Error creando respaldo: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Detecta la ruta de mysqldump según el sistema operativo
     */
    private function detectMysqldumpPath()
    {
        // Para Windows (XAMPP)
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $possiblePaths = [
                'C:\\xampp\\mysql\\bin\\mysqldump.exe',
                'C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\mysqldump.exe',
                'C:\\Program Files\\MySQL\\MySQL Server 5.7\\bin\\mysqldump.exe',
                'mysqldump.exe', // Si está en el PATH
            ];
        } else {
            // Para Linux/Mac
            $possiblePaths = [
                '/usr/bin/mysqldump',
                '/usr/local/bin/mysqldump',
                '/opt/lampp/bin/mysqldump',
                'mysqldump', // Si está en el PATH
            ];
        }

        foreach ($possiblePaths as $path) {
            if (@is_executable($path) || @file_exists($path)) {
                return $path;
            }
        }

        // Intentar encontrar usando 'which' o 'where'
        $findCommand = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' ? 'where mysqldump' : 'which mysqldump';
        $output = shell_exec($findCommand);
        
        if ($output) {
            $path = trim($output);
            if (file_exists($path)) {
                return $path;
            }
        }

        return null;
    }
}