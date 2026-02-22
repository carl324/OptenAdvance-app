<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Notification;
use App\Services\LicenseService;
use Illuminate\Support\Facades\Log;

class VerificarLicencia extends Command
{
    protected $signature = 'licencia:verificar';
    protected $description = 'Verifica el estado de la licencia y envía notificaciones si está próxima a vencer';

    public function handle()
    {
        try {
            $service = new LicenseService();
            $data = $service->uiData();

            $status = $data['status'];
            $diasRestantes = $data['days_remaining'];

            // Si no hay licencia activa o no hay días restantes, no hacer nada
if (!in_array($status, ['active', 'trial_active']) || $diasRestantes === null) {
    $this->info('Licencia no activa o sin datos de vencimiento');
    return 0;
}

// Solo notificar si es licencia full
if ($data['type'] !== 'full') {
    $this->info('Licencia trial — no se generan notificaciones de vencimiento');
    return 0;
}

            $this->info("Días restantes: {$diasRestantes}");

            // Umbrales de notificación
            $umbrales = [
    3  => ['tipo' => 'error',   'titulo' => 'Licencia vence en 3 días'],
    7  => ['tipo' => 'error',   'titulo' => 'Licencia próxima a vencer — 7 días'],
    15 => ['tipo' => 'warning', 'titulo' => 'Licencia próxima a vencer — 15 días'],
    30 => ['tipo' => 'warning', 'titulo' => 'Licencia próxima a vencer — 30 días'],
];

            foreach ($umbrales as $dias => $config) {
                if ($diasRestantes <= $dias) {
                    $this->notificar(
                        $config['tipo'],
                        $config['titulo'],
                        'Tu licencia vence en ' . $diasRestantes . ' día(s). Contacta a soporte para renovarla y evitar interrupciones en el sistema.',
                        ['dias_restantes' => $diasRestantes, 'fecha_fin' => $data['end_at']]
                    );
                    break; // Solo una notificación por ejecución
                }
            }

            return 0;

        } catch (\Exception $e) {
            Log::error('Error verificando licencia: ' . $e->getMessage());
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
    }

    private function notificar(string $tipo, string $titulo, string $mensaje, array $data = []): void
    {
        try {
            // No crear duplicado si ya hay una notificación no leída del mismo título
            $yaExiste = Notification::where('modulo', 'licencia')
                ->where('titulo', $titulo)
                ->where('leida', false)
                ->exists();

            if (!$yaExiste) {
                Notification::crear('licencia', $tipo, $titulo, $mensaje, $data);
                $this->info('✓ Notificación creada: ' . $titulo);
            } else {
                $this->info('Notificación ya existe: ' . $titulo);
            }
        } catch (\Exception $e) {
            Log::error('Error creando notificación de licencia: ' . $e->getMessage());
        }
    }
}