<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Http\UploadedFile;

class DatabaseRestoreService
{
    private const MAX_FILE_SIZE = 524288000;
    private const ALLOWED_EXTENSIONS = ['sql'];
    private const ALLOWED_MIMES = ['text/plain', 'text/x-sql', 'application/sql', 'application/x-sql', 'application/octet-stream'];

    public function validateBackupFile(UploadedFile $file): array
    {
        $errors = [];

        if ($file->getSize() > self::MAX_FILE_SIZE) {
            $errors[] = 'El archivo excede el tamaño máximo permitido (500MB)';
        }

        if ($file->getSize() < 100) {
            $errors[] = 'El archivo está vacío o corrupto';
        }

        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, self::ALLOWED_EXTENSIONS)) {
            $errors[] = 'Solo se permiten archivos .sql';
        }

        $mimeType = $file->getMimeType();
        if (!in_array($mimeType, self::ALLOWED_MIMES)) {
            $errors[] = 'Tipo de archivo no válido. Debe ser un archivo SQL';
        }

        $handle = fopen($file->getRealPath(), 'r');
        if ($handle) {
            $header = fread($handle, 2000);
            fclose($handle);
            
            if (stripos($header, 'mysqldump: [Warning]') !== false) {
                $errors[] = 'El archivo SQL está corrupto (contiene warnings de mysqldump). Genera un nuevo respaldo desde "Crear respaldo".';
            }
            
            $hasValidSQL = (
                stripos($header, 'CREATE TABLE') !== false ||
                stripos($header, 'INSERT INTO') !== false ||
                stripos($header, 'DROP TABLE') !== false ||
                stripos($header, '-- MySQL dump') !== false ||
                stripos($header, 'SET @') !== false
            );

            if (!$hasValidSQL) {
                $errors[] = 'El archivo no parece contener un respaldo SQL válido';
            }
        } else {
            $errors[] = 'No se pudo leer el archivo';
        }

        return $errors;
    }

    public function createAutoBackup(): array
    {
        try {
            $dbHost = Config::get('database.connections.mysql.host');
            $dbPort = Config::get('database.connections.mysql.port', 3306);
            $dbName = Config::get('database.connections.mysql.database');
            $dbUser = Config::get('database.connections.mysql.username');
            $dbPass = Config::get('database.connections.mysql.password');

            $backupDir = storage_path('app' . DIRECTORY_SEPARATOR . 'auto-backups');
            if (!is_dir($backupDir)) {
                mkdir($backupDir, 0755, true);
            }

            $timestamp = date('Y-m-d_H-i-s');
            $fileName = "auto_backup_{$timestamp}.sql";
            $filePath = $backupDir . DIRECTORY_SEPARATOR . $fileName;

            $mysqldumpPath = $this->detectLaragonMysqldump();
            if (!$mysqldumpPath) {
                throw new \Exception('No se encontró mysqldump en Laragon');
            }

            $command = sprintf(
                '"%s" --host=%s --port=%s --user=%s --password=%s --single-transaction --routines --triggers %s > "%s" 2>NUL',
                $mysqldumpPath,
                $dbHost,
                $dbPort,
                $dbUser,
                $dbPass,
                $dbName,
                $filePath
            );

            exec($command, $output, $returnVar);

            if ($returnVar !== 0 || !file_exists($filePath) || filesize($filePath) < 100) {
                throw new \Exception('Error al crear backup automático');
            }

            Log::info('DatabaseRestoreService: Backup automático creado', ['file' => $fileName, 'size' => filesize($filePath)]);

            return ['success' => true, 'file' => $fileName, 'path' => $filePath];

        } catch (\Exception $e) {
            Log::error('DatabaseRestoreService: Error en backup automático', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function restoreDatabase(string $sqlFilePath): array
    {
        try {
            $dbHost = Config::get('database.connections.mysql.host');
            $dbPort = Config::get('database.connections.mysql.port', 3306);
            $dbName = Config::get('database.connections.mysql.database');
            $dbUser = Config::get('database.connections.mysql.username');
            $dbPass = Config::get('database.connections.mysql.password');

            $mysqlPath = $this->detectLaragonMysql();
            if (!$mysqlPath) {
                throw new \Exception('No se encontró mysql.exe en Laragon');
            }

            // FIX: Aumentar límites para archivos grandes
            set_time_limit(600);
            ini_set('memory_limit', '512M');

            $command = sprintf(
                '"%s" --host=%s --port=%s --user=%s --password=%s --default-character-set=utf8mb4 %s < "%s" 2>NUL',
                $mysqlPath,
                $dbHost,
                $dbPort,
                $dbUser,
                $dbPass,
                $dbName,
                $sqlFilePath
            );

            exec($command, $output, $returnVar);

            if ($returnVar !== 0) {
                $errorMsg = implode("\n", $output);
                throw new \Exception('Error al restaurar: ' . $errorMsg);
            }

            Log::info('DatabaseRestoreService: Base de datos restaurada exitosamente', ['file' => basename($sqlFilePath)]);

            return ['success' => true];

        } catch (\Exception $e) {
            Log::error('DatabaseRestoreService: Error en restauración', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Detecta la ruta de mysql.exe con múltiples estrategias
     */
    private function detectLaragonMysql(): ?string
    {
        $basePath = base_path();
        
        // ESTRATEGIA 1: Buscar en múltiples niveles superiores
        $levelsUp = [
            dirname($basePath),                    // ../
            dirname(dirname($basePath)),           // ../../
            dirname(dirname(dirname($basePath)))   // ../../../
        ];
        
        foreach ($levelsUp as $laragonBase) {
            $mysqlDir = $laragonBase . DIRECTORY_SEPARATOR . 'laragon' . DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR . 'mysql';
            
            if (is_dir($mysqlDir)) {
                $versions = @scandir($mysqlDir);
                if ($versions) {
                    foreach ($versions as $version) {
                        if ($version === '.' || $version === '..') continue;
                        $mysqlExe = $mysqlDir . DIRECTORY_SEPARATOR . $version . DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR . 'mysql.exe';
                        if (file_exists($mysqlExe)) {
                            Log::info("DatabaseRestoreService - mysql.exe encontrado en: {$mysqlExe}");
                            return $mysqlExe;
                        }
                    }
                }
            }
        }
        
        // ESTRATEGIA 2: Buscar en el PATH del sistema
        $output = [];
        exec('where mysql.exe 2>nul', $output, $returnVar);
        if ($returnVar === 0 && !empty($output[0]) && file_exists($output[0])) {
            Log::info("DatabaseRestoreService - mysql.exe encontrado en PATH: {$output[0]}");
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
                        $mysqlExe = $mysqlDir . DIRECTORY_SEPARATOR . $version . DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR . 'mysql.exe';
                        if (file_exists($mysqlExe)) {
                            Log::info("DatabaseRestoreService - mysql.exe encontrado en ruta común: {$mysqlExe}");
                            return $mysqlExe;
                        }
                    }
                }
            }
        }
        
        Log::error('DatabaseRestoreService - mysql.exe NO encontrado. Rutas revisadas:', [
            'base_path' => $basePath,
            'levels_checked' => $levelsUp
        ]);
        
        return null;
    }

    /**
     * Detecta la ruta de mysqldump con múltiples estrategias
     */
    private function detectLaragonMysqldump(): ?string
    {
        $basePath = base_path();
        
        // ESTRATEGIA 1: Buscar en múltiples niveles superiores
        $levelsUp = [
            dirname($basePath),                    // ../
            dirname(dirname($basePath)),           // ../../
            dirname(dirname(dirname($basePath)))   // ../../../
        ];
        
        foreach ($levelsUp as $laragonBase) {
            $mysqlDir = $laragonBase . DIRECTORY_SEPARATOR . 'laragon' . DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR . 'mysql';
            
            if (is_dir($mysqlDir)) {
                $versions = @scandir($mysqlDir);
                if ($versions) {
                    foreach ($versions as $version) {
                        if ($version === '.' || $version === '..') continue;
                        $mysqldump = $mysqlDir . DIRECTORY_SEPARATOR . $version . DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR . 'mysqldump.exe';
                        if (file_exists($mysqldump)) {
                            Log::info("DatabaseRestoreService - mysqldump encontrado en: {$mysqldump}");
                            return $mysqldump;
                        }
                    }
                }
            }
        }
        
        // ESTRATEGIA 2: Buscar en el PATH del sistema
        $output = [];
        exec('where mysqldump.exe 2>nul', $output, $returnVar);
        if ($returnVar === 0 && !empty($output[0]) && file_exists($output[0])) {
            Log::info("DatabaseRestoreService - mysqldump encontrado en PATH: {$output[0]}");
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
                            Log::info("DatabaseRestoreService - mysqldump encontrado en ruta común: {$mysqldump}");
                            return $mysqldump;
                        }
                    }
                }
            }
        }
        
        Log::error('DatabaseRestoreService - mysqldump NO encontrado. Rutas revisadas:', [
            'base_path' => $basePath,
            'levels_checked' => $levelsUp
        ]);
        
        return null;
    }

    public function cleanupOldBackups(int $daysToKeep = 7): void
    {
        $backupDir = storage_path('app' . DIRECTORY_SEPARATOR . 'auto-backups');
        
        if (!is_dir($backupDir)) {
            return;
        }

        $files = File::files($backupDir);
        $threshold = time() - ($daysToKeep * 24 * 60 * 60);

        foreach ($files as $file) {
            if ($file->getMTime() < $threshold) {
                @unlink($file->getPathname());
                Log::info('DatabaseRestoreService: Backup antiguo eliminado', ['file' => $file->getFilename()]);
            }
        }
    }
}