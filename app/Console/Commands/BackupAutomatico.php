<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ConfiguracionBackup;
use App\Models\Notification;
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
                Log::error('Backup automático: No hay configuración definida en la DB');
                $this->error('No hay configuración de backup definida');
                return 1;
            }

            if (!$this->debeEjecutarBackup($config)) {
                $this->info('No es momento de ejecutar backup');
                return 0;
            }

            $this->info('Iniciando backup automático...');

            $resultado = $this->crearBackup($config);

            if ($resultado['success']) {
                $config->update(['ultima_fecha_backup' => now()]);
                $this->limpiarBackupsAntiguos($config);
                $this->info('✓ Backup completado: ' . $resultado['archivo']);
                Log::info('Backup automático exitoso: ' . $resultado['archivo']);
                return 0;
            } else {
                $this->error('✗ Error: ' . $resultado['error']);
                Log::error('Backup automático fallido: ' . $resultado['error']);
                return 1;
            }

        } catch (\Exception $e) {
            $this->error('Error inesperado: ' . $e->getMessage());
            Log::error('Error inesperado en backup automático: ' . $e->getMessage());
            return 1;
        }
    }

    private function debeEjecutarBackup(ConfiguracionBackup $config): bool
    {
        $ahora = Carbon::now();

        $horaConfig    = Carbon::createFromFormat('H:i:s', $config->hora_backup);
        $minutosAhora  = $ahora->hour * 60 + $ahora->minute;
        $minutosConfig = $horaConfig->hour * 60 + $horaConfig->minute;

        if (abs($minutosAhora - $minutosConfig) > 5) {
            return false;
        }

        if (!$config->ultima_fecha_backup) {
            return true;
        }

        $ultimoBackup = Carbon::parse($config->ultima_fecha_backup);

        switch ($config->frecuencia) {
            case 'diario':
                return $ultimoBackup->diffInHours($ahora) >= 23;
            case 'semanal':
                return $ultimoBackup->diffInDays($ahora) >= 6;
            case 'mensual':
                return $ultimoBackup->diffInDays($ahora) >= 28;
            default:
                return false;
        }
    }

    private function crearBackup(ConfiguracionBackup $config): array
    {
        $archivoCredenciales = null;

        try {
            $carpeta = rtrim(str_replace('/', DIRECTORY_SEPARATOR, $config->carpeta_destino), DIRECTORY_SEPARATOR);

            // Error #1: mysqldump no encontrado
            $mysqldump = $this->encontrarMysqldump();
            if (!$mysqldump) {
                $this->notificarError(
                    'mysqldump no encontrado',
                    'No se encontró el ejecutable mysqldump en el sistema. Verifica que MySQL esté instalado correctamente.',
                    ['carpeta' => $carpeta]
                );
                return ['success' => false, 'error' => 'No se encontró mysqldump en el sistema'];
            }

            // Error #2: Carpeta no existe
            if (!File::exists($carpeta) && !is_dir($carpeta)) {
                $this->notificarError(
                    'Carpeta de backup no encontrada',
                    'La carpeta destino "' . $carpeta . '" no existe. Verifica la ruta en la configuración de backup.',
                    ['carpeta' => $carpeta]
                );
                return ['success' => false, 'error' => 'Carpeta no existe: ' . $carpeta];
            }

            // Error #3: Sin permisos — verificación real intentando escribir
            $archivoPrueba = $carpeta . DIRECTORY_SEPARATOR . '.write_test_' . time();
            $puedeEscribir = @file_put_contents($archivoPrueba, 'test') !== false;
            if (file_exists($archivoPrueba)) @unlink($archivoPrueba);

            if (!$puedeEscribir) {
                $this->notificarError(
                    'Sin permisos en carpeta de backup',
                    'El sistema no tiene permisos de escritura en "' . $carpeta . '". Si es una carpeta de Google Drive, configura una ruta local en su lugar.',
                    ['carpeta' => $carpeta]
                );
                return ['success' => false, 'error' => 'Sin permisos: ' . $carpeta];
            }

            $timestamp     = now()->format('Y-m-d_His');
            $nombreArchivo = $config->prefijo_nombre_archivo . '_' . $timestamp . '.sql';
            $rutaCompleta  = $carpeta . DIRECTORY_SEPARATOR . $nombreArchivo;

            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');
            $host     = config('database.connections.mysql.host');
            $port     = config('database.connections.mysql.port', 3306);

            $archivoCredenciales = tempnam(sys_get_temp_dir(), 'mysql_backup_');
            File::put($archivoCredenciales, "[client]\nuser=" . $username . "\npassword=" . $password . "\nhost=" . $host . "\nport=" . $port . "\n");

            $comando = sprintf(
                '"%s" --defaults-extra-file="%s" %s --result-file="%s" 2>&1',
                $mysqldump,
                $archivoCredenciales,
                $database,
                $rutaCompleta
            );

            exec($comando, $output, $returnVar);

            File::delete($archivoCredenciales);
            $archivoCredenciales = null;

            // Error #4: Archivo vacío o no se creó
            if ($returnVar !== 0 || !File::exists($rutaCompleta) || File::size($rutaCompleta) === 0) {
                if (File::exists($rutaCompleta)) {
                    File::delete($rutaCompleta);
                }
                $detalle = implode(' | ', $output);
$detalle = mb_detect_encoding($detalle, 'UTF-8', true) 
    ? $detalle 
    : mb_convert_encoding($detalle, 'UTF-8', 'CP850');
                $this->notificarError(
                    'Error al generar el archivo de backup',
                    'mysqldump no pudo generar el archivo de respaldo. Detalle: ' . ($detalle ?: 'Sin detalles disponibles'),
                    ['carpeta' => $carpeta, 'detalle' => $detalle]
                );
                return ['success' => false, 'error' => 'Error al ejecutar mysqldump: ' . $detalle];
            }

            return [
                'success' => true,
                'archivo' => $nombreArchivo,
                'ruta'    => $rutaCompleta
            ];

        } catch (\Exception $e) {
    if ($archivoCredenciales && File::exists($archivoCredenciales)) {
        File::delete($archivoCredenciales);
    }
    $detalle = $e->getMessage();
    $detalle = mb_detect_encoding($detalle, 'UTF-8', true)
        ? $detalle
        : mb_convert_encoding($detalle, 'UTF-8', 'CP850');
    $this->notificarError(
        'Error inesperado en el backup',
        'Ocurrió un error inesperado al intentar crear el respaldo. Sugerencias: verifica que la carpeta destino exista y tenga permisos de escritura, que MySQL esté corriendo y que haya espacio disponible en disco.',
        ['detalle' => $detalle]
    );
    return ['success' => false, 'error' => $detalle];
}
    }

    /**
     * Crea una notificación de error evitando duplicados en las últimas 24h.
     */
    private function notificarError(string $titulo, string $mensaje, array $data = []): void
    {
        try {
          $yaExiste = Notification::where('modulo', 'backup')
    ->where('titulo', $titulo)
    ->where('leida', false)
    ->exists();

            if (!$yaExiste) {
                Notification::crear('backup', 'error', $titulo, $mensaje, $data);
            }
        } catch (\Exception $e) {
            Log::error('Error creando notificación de backup: ' . $e->getMessage());
        }
    }

    private function encontrarMysqldump(): ?string
    {
        $rutasComunes = [
            'C:\\optenadvance\\app\\mysql\\bin\\mysqldump.exe',
            'C:\\xampp\\mysql\\bin\\mysqldump.exe',
            'C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\mysqldump.exe',
            'C:\\Program Files\\MySQL\\MySQL Server 5.7\\bin\\mysqldump.exe',
            'C:\\Program Files (x86)\\MySQL\\MySQL Server 8.0\\bin\\mysqldump.exe',
            'C:\\laragon\\bin\\mysql\\mysql-8.0\\bin\\mysqldump.exe',
            'C:\\laragon\\bin\\mysql\\mysql-5.7\\bin\\mysqldump.exe',
        ];

        foreach ($rutasComunes as $ruta) {
            if (file_exists($ruta)) {
                return $ruta;
            }
        }

        exec('mysqldump --version 2>&1', $out, $code);
        if ($code === 0) {
            return 'mysqldump';
        }

        return null;
    }

    private function limpiarBackupsAntiguos(ConfiguracionBackup $config): void
    {
        try {
            $carpeta   = rtrim(str_replace('/', DIRECTORY_SEPARATOR, $config->carpeta_destino), DIRECTORY_SEPARATOR);
            $prefijo   = $config->prefijo_nombre_archivo;
            $retencion = $config->retencion;

            if (!File::exists($carpeta)) {
                return;
            }

            $backups = collect(File::files($carpeta))
                ->filter(fn($f) => str_starts_with(basename($f), $prefijo) && str_ends_with(basename($f), '.sql'))
                ->sortByDesc(fn($f) => File::lastModified($f))
                ->values();

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