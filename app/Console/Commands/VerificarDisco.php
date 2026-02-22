<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;

class VerificarDisco extends Command
{
    protected $signature = 'disco:verificar';
    protected $description = 'Verifica el espacio disponible en disco y notifica si está bajo';

    // Umbrales en bytes
    private const WARNING_GB = 5; // menos de 5GB → warning
    private const ERROR_GB   = 2; // menos de 2GB → error

    public function handle()
    {
        try {
            $unidad = 'C:\\';
            $libreBytes = disk_free_space($unidad);

            if ($libreBytes === false) {
                Log::error('VerificarDisco: No se pudo obtener espacio libre en ' . $unidad);
                $this->error('No se pudo obtener espacio libre');
                return 1;
            }

            $libreGB = round($libreBytes / (1024 ** 3), 2);
            $this->info("Espacio libre en C:\\: {$libreGB} GB");

            if ($libreGB < self::ERROR_GB) {
                $this->notificar(
                    'error',
                    'Espacio en disco crítico',
                    "Quedan solo {$libreGB} GB libres en el disco C:\\. El sistema puede dejar de guardar ventas y datos en cualquier momento. Libera espacio urgentemente.",
                    ['libre_gb' => $libreGB, 'unidad' => $unidad]
                );
            } elseif ($libreGB < self::WARNING_GB) {
                $this->notificar(
                    'warning',
                    'Espacio en disco bajo',
                    "Quedan {$libreGB} GB libres en el disco C:\\. Se recomienda liberar espacio pronto para evitar problemas en el sistema.",
                    ['libre_gb' => $libreGB, 'unidad' => $unidad]
                );
            } else {
                $this->info('Espacio en disco suficiente — sin notificaciones');
            }

            return 0;

        } catch (\Exception $e) {
            Log::error('Error verificando disco: ' . $e->getMessage());
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
    }

    private function notificar(string $tipo, string $titulo, string $mensaje, array $data = []): void
    {
        try {
            $yaExiste = Notification::where('modulo', 'sistema')
                ->where('titulo', $titulo)
                ->where('created_at', '>=', now()->subDays(7))
                ->exists();

            if (!$yaExiste) {
                Notification::crear('sistema', $tipo, $titulo, $mensaje, $data);
                $this->info('✓ Notificación creada: ' . $titulo);
            } else {
                $this->info('Notificación ya existe: ' . $titulo);
            }
        } catch (\Exception $e) {
            Log::error('Error creando notificación de disco: ' . $e->getMessage());
        }
    }
}