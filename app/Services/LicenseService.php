<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;

class LicenseService
{
    private string $path;
    private const CACHE_TTL = 86400; // 24 horas en segundos

    public function __construct()
    {
        $this->path = storage_path('app/license/license.lic');
    }

    /**
     * Estado de licencia con caché de 24 horas
     */
    public function status(): string
    {
        return Cache::remember('license_status', self::CACHE_TTL, function () {
            return $this->checkLicenseStatus();
        });
    }

    /**
     * Datos para UI con caché de 24 horas
     */
    public function uiData(): array
    {
        return Cache::remember('license_ui_data', self::CACHE_TTL, function () {
            return $this->generateUiData();
        });
    }

    /**
     * Forzar revalidación (llamar cuando se actualice la licencia)
     */
    public function refresh(): void
    {
        Cache::forget('license_status');
        Cache::forget('license_ui_data');
        Cache::forget('license_machine_hash');
    }

    /**
     * Lógica real de validación (privada, solo se ejecuta si no hay caché)
     */
    private function checkLicenseStatus(): string
    {
        $now = Carbon::now();

        // 1. Archivo no existe → primer trial
        if (!file_exists($this->path)) {
            return $this->persistState('trial_first', null, $now);
        }

        $raw = @file_get_contents($this->path);
        if ($raw === false || strlen($raw) < 17) {
            return $this->persistState('expired', null, $now);
        }

        $key    = hash('sha256', config('app.key'), true);
        $iv     = substr($raw, 0, 16);
        $cipher = substr($raw, 16);

        $plain = openssl_decrypt(
            $cipher,
            'AES-256-CBC',
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );

        if ($plain === false) {
            return $this->persistState('expired', null, $now);
        }

        // 2. Estructura correcta del payload
        $parts = explode('|', $plain);
        if (count($parts) !== 5) {
            return $this->persistState('expired', null, $now);
        }

        [$type, $machineHash, $startAt, $endAt, $sig] = $parts;

        // 3. Firma
        $expected = hash_hmac(
            'sha256',
            $type . '|' . $machineHash . '|' . $startAt . '|' . $endAt,
            config('app.key')
        );

        if (!hash_equals($expected, $sig)) {
            return $this->persistState('expired', null, $now);
        }

        // 4. Hardware (con caché del hash)
        if ($machineHash !== $this->machineHash()) {
            return $this->persistState('trial_hardware', $machineHash, $now);
        }

        // 5. Vencimiento real
        if ($now->gt(Carbon::parse($endAt))) {
            return $this->persistState('expired', $machineHash, $now);
        }

        // 6. Anti rollback de fecha en BD
        $row = DB::table('license_state')->first();
        if ($row && $row->last_valid_check_at &&
            $now->lt(Carbon::parse($row->last_valid_check_at)->subHours(12))) {
            return $this->persistState('expired', $machineHash, $now);
        }

        // 7. Retornar estado según tipo de licencia
        if ($type === 'trial') {
            return $this->persistState('trial_active', $machineHash, $now);
        }

        return $this->persistState('active', $machineHash, $now);
    }

    /**
     * Generar datos para UI (privado)
     */
    private function generateUiData(): array
    {
        $status = $this->checkLicenseStatus(); // Usa versión sin caché interna

        $data = [
            'status' => $status,
            'start_at' => null,
            'end_at' => null,
            'days_remaining' => null,
            'show_notification' => false,
        ];

        if (file_exists($this->path)) {
            $raw = @file_get_contents($this->path);
            if ($raw && strlen($raw) > 16) {
                $key    = hash('sha256', config('app.key'), true);
                $iv     = substr($raw, 0, 16);
                $cipher = substr($raw, 16);

                $plain = openssl_decrypt($cipher, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);

                if ($plain) {
                    $parts = explode('|', $plain);
                    if (count($parts) === 5) {
                        $startAt = Carbon::parse($parts[2]);
                        $endAt   = Carbon::parse($parts[3]);

                        $data['start_at'] = $startAt->format('M d, Y');
                        $data['end_at'] = $endAt->format('M d, Y');
                        
                        $daysRemaining = now()->diffInDays($endAt, false);
                        $data['days_remaining'] = (int) ceil($daysRemaining);
                        
                        $data['show_notification'] = in_array($status, ['active', 'trial_active']) 
                                                      && $data['days_remaining'] >= 0 
                                                      && $data['days_remaining'] <= 30;
                    }
                }
            }
        }

        return $data;
    }

    private function persistState(string $status, ?string $machineHash, $now): string
    {
        DB::table('license_state')->updateOrInsert(
            ['id' => 1],
            [
                'status' => $status,
                'machine_hash' => $machineHash ?? $this->machineHash(),
                'last_valid_check_at' => $now,
                'updated_at' => $now,
                'created_at' => $now,
            ]
        );

        return $status;
    }

    /**
     * Machine hash con caché (el comando exec es LENTO)
     */
    private function machineHash(): string
    {
        return Cache::remember('license_machine_hash', 604800, function () { // 7 días
            $hostname = gethostname() ?: 'unknown';
            $mac = exec('getmac');
            $mac = preg_replace('/\s+/', '', $mac);

            return hash('sha256', $mac . $hostname . 'FIXED_SALT');
        });
    }
}