<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ConfiguracionBackup;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BackupAutomatico extends Command
{
    protected $signature = 'backup:automatico';
    protected $description = 'Ejecuta respaldo automático de la base de datos según configuración';

    public function handle()
    {
        try {
            $config = ConfiguracionBackup::first();
            
            if (!$config) {
                $this->error('No hay configuración de backup definida');
                return 1;
            }

            // Verificar si debe ejecutarse según frecuencia y hora
            if (!$this->debeEjecutarBackup($config)) {
                $this->info('No es momento de ejecutar backup');
                return 0;
            }

            $this->info('Iniciando backup automático...');

            // Crear backup
            $resultado = $this->crearBackup($config);

            if ($resultado['success']) {
                // Actualizar última fecha de backup
                $config->update(['ultima_fecha_backup' => now()]);
                
                // Limpiar backups antiguos según retención
                $this->limpiarBackupsAntiguos($config);
                
                $this->info('✓ Backup completado exitosamente: ' . $resultado['archivo']);
                Log::info('Backup automático exitoso: ' . $resultado['archivo']);
                return 0;
            } else {
                $this->error('✗ Error al crear backup: ' . $resultado['error']);
                Log::error('Error en backup automático: ' . $resultado['error']);
                return 1;
            }

        } catch (\Exception $e) {
            $this->error('Error inesperado: ' . $e->getMessage());
            Log::error('Error en backup automático: ' . $e->getMessage());
            return 1;
        }
    }

    private function debeEjecutarBackup(ConfiguracionBackup $config): bool
    {
        $ahora = Carbon::now();
        $horaConfig = Carbon::createFromFormat('H:i:s', $config->hora_backup);
        
        // Verificar si estamos en la hora correcta (con margen de 30 min)
        $horaActual = $ahora->format('H:i');
        $horaObjetivo = $horaConfig->format('H:i');
        
        if ($horaActual !== $horaObjetivo) {
            return false;
        }

        // Si no hay backup previo, ejecutar
        if (!$config->ultima_fecha_backup) {
            return true;
        }

        $ultimoBackup = Carbon::parse($config->ultima_fecha_backup);

        // Verificar según frecuencia
        switch ($config->frecuencia) {
            case 'diario':
                return $ultimoBackup->diffInDays($ahora) >= 1;
            
            case 'semanal':
                return $ultimoBackup->diffInWeeks($ahora) >= 1;
            
            case 'mensual':
                return $ultimoBackup->diffInMonths($ahora) >= 1;
            
            default:
                return false;
        }
    }

    private function crearBackup(ConfiguracionBackup $config): array
    {
        try {
            // Verificar que la carpeta destino existe
            if (!File::exists($config->carpeta_destino)) {
                File::makeDirectory($config->carpeta_destino, 0755, true);
            }

            // Generar nombre del archivo
            $timestamp = now()->format('Y-m-d_His');
            $nombreArchivo = $config->prefijo_nombre_archivo . '_' . $timestamp . '.sql';
            $rutaCompleta = $config->carpeta_destino . '/' . $nombreArchivo;

            // Obtener credenciales de la base de datos
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');
            $host = config('database.connections.mysql.host');

            // Comando mysqldump
            $comando = sprintf(
                'mysqldump --user=%s --password=%s --host=%s %s > %s 2>&1',
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($host),
                escapeshellarg($database),
                escapeshellarg($rutaCompleta)
            );

            // Ejecutar backup
            exec($comando, $output, $returnVar);

            if ($returnVar !== 0 || !File::exists($rutaCompleta) || File::size($rutaCompleta) === 0) {
                return [
                    'success' => false,
                    'error' => 'Error al ejecutar mysqldump: ' . implode("\n", $output)
                ];
            }

            return [
                'success' => true,
                'archivo' => $nombreArchivo,
                'ruta' => $rutaCompleta
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    private function limpiarBackupsAntiguos(ConfiguracionBackup $config): void
    {
        try {
            $carpeta = $config->carpeta_destino;
            $prefijo = $config->prefijo_nombre_archivo;
            $retencion = $config->retencion;

            if (!File::exists($carpeta)) {
                return;
            }

            // Obtener todos los archivos de backup
            $archivos = File::files($carpeta);
            
            // Filtrar solo los archivos que coincidan con el prefijo
            $backups = collect($archivos)
                ->filter(function ($archivo) use ($prefijo) {
                    return str_starts_with(basename($archivo), $prefijo);
                })
                ->sortByDesc(function ($archivo) {
                    return File::lastModified($archivo);
                });

            // Si hay más archivos que la retención, eliminar los más antiguos
            if ($backups->count() > $retencion) {
                $backups->skip($retencion)->each(function ($archivo) {
                    File::delete($archivo);
                    Log::info('Backup antiguo eliminado: ' . basename($archivo));
                });
            }

        } catch (\Exception $e) {
            Log::error('Error limpiando backups antiguos: ' . $e->getMessage());
        }
    }
}