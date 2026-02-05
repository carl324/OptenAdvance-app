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
            $lockFile = storage_path('app' . DIRECTORY_SEPARATOR . 'backup.lock');
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

            // 6) Detectar ruta de mysqldump desde Laragon portable
            $mysqldumpPath = $this->detectLaragonMysqldump();
            
            if (!$mysqldumpPath || !file_exists($mysqldumpPath)) {
                Log::error('BackupController::store - mysqldump no encontrado en Laragon');
                @unlink($lockFile);
                return response()->json(['error' => 'No se encontró mysqldump en Laragon portable.'], 500);
            }

            // 7) Construir comando mysqldump para Windows (sin ventana visible)
            $command = sprintf(
    '"%s" --host=%s --port=%s --user=%s --password=%s --single-transaction --routines --triggers %s > "%s" 2> "%s"',
    $mysqldumpPath,
    $dbHost,
    $dbPort,
    $dbUser,
    $dbPass,
    $dbName,
    $target,
    $tempDir . DIRECTORY_SEPARATOR . 'warnings.log'
);


            // Ejecutar comando sin mostrar ventana en Windows
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

            // 8) Validar que el archivo se creó y tiene contenido
            if (!file_exists($target) || filesize($target) < 100) {
                Log::error('BackupController::store - Archivo de backup vacío o no creado: ' . $target);
                @unlink($target);
                @unlink($lockFile);
                return response()->json(['error' => 'El archivo de respaldo está vacío o no se generó correctamente.'], 500);
            }

            Log::info('BackupController::store - Backup MySQL completado exitosamente: ' . $fileName . ' (' . filesize($target) . ' bytes)');

            // 9) Limpieza y retornar descarga
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
     * Detecta la ruta de mysqldump desde Laragon portable
     */
    /**
 * Detecta la ruta de mysqldump desde Laragon portable con múltiples estrategias
 */
private function detectLaragonMysqldump()
{
    $basePath = base_path();
    
    // ESTRATEGIA 1: Buscar en múltiples niveles superiores (proyecto puede estar en www/nombre-proyecto)
    $levelsUp = [
        dirname($basePath),                    // ../
        dirname(dirname($basePath)),           // ../../
        dirname(dirname(dirname($basePath)))   // ../../../
    ];
    
    foreach ($levelsUp as $laragonBase) {
        // Buscar en bin/mysql/*/bin/mysqldump.exe
        $mysqlDir = $laragonBase . DIRECTORY_SEPARATOR . 'laragon' . DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR . 'mysql';
        
        if (is_dir($mysqlDir)) {
            $versions = @scandir($mysqlDir);
            if ($versions) {
                foreach ($versions as $version) {
                    if ($version === '.' || $version === '..') continue;
                    $mysqldump = $mysqlDir . DIRECTORY_SEPARATOR . $version . DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR . 'mysqldump.exe';
                    if (file_exists($mysqldump)) {
                        Log::info("BackupController - mysqldump encontrado en: {$mysqldump}");
                        return $mysqldump;
                    }
                }
            }
        }
    }
    
    // ESTRATEGIA 2: Buscar mysqldump en el PATH del sistema
    $output = [];
    exec('where mysqldump.exe 2>nul', $output, $returnVar);
    if ($returnVar === 0 && !empty($output[0]) && file_exists($output[0])) {
        Log::info("BackupController - mysqldump encontrado en PATH: {$output[0]}");
        return $output[0];
    }
    
    // ESTRATEGIA 3: Buscar en ubicaciones comunes de Laragon
    $commonPaths = [
        'C:\\laragon\\bin\\mysql',
        'D:\\laragon\\bin\\mysql',
        getenv('USERPROFILE') . '\\laragon\\bin\\mysql'
    ];
    
    foreach ($commonPaths as $mysqlDir) {
        if (is_dir($mysqlDir)) {
            $versions = @scandir($mysqlDir);
            if ($versions) {
                foreach ($versions as $version) {
                    if ($version === '.' || $version === '..') continue;
                    $mysqldump = $mysqlDir . DIRECTORY_SEPARATOR . $version . DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR . 'mysqldump.exe';
                    if (file_exists($mysqldump)) {
                        Log::info("BackupController - mysqldump encontrado en ruta común: {$mysqldump}");
                        return $mysqldump;
                    }
                }
            }
        }
    }
    
    // Log de rutas intentadas para debugging
    Log::error('BackupController - mysqldump NO encontrado. Rutas revisadas:', [
        'base_path' => $basePath,
        'levels_checked' => $levelsUp
    ]);
    
    return null;
}
}