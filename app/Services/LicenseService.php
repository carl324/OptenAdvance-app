<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;

class LicenseService
{
    private const MASTER_KEY = '145537332a7bf08db92cb37b3b752588c127fbb85959b5f577ab70b08d154956';
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
     * Instalar/cargar archivo de licencia
     */
    /**
 * Instalar/cargar archivo de licencia
 */
public function install($file): array
{
    try {
        // Leer contenido del archivo subido ANTES de moverlo
        $raw = file_get_contents($file->getRealPath());
        
        if ($raw === false || strlen($raw) < 17) {
            return [
                'success' => false,
                'message' => 'Archivo de licencia corrupto o vacío'
            ];
        }

        // Validar que se pueda descifrar
        $key = hash('sha256', self::MASTER_KEY, true);
        $iv = substr($raw, 0, 16);
        $cipher = substr($raw, 16);

        $plain = openssl_decrypt($cipher, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);

        if ($plain === false) {
            return [
                'success' => false,
                'message' => 'Licencia inválida o corrupta'
            ];
        }

        // Validar estructura
        $parts = explode('|', $plain);
        if (count($parts) !== 5) {
            return [
                'success' => false,
                'message' => 'Formato de licencia inválido'
            ];
        }

        [$type, $machineHash, $startAt, $endAt, $sig] = $parts;

        // Validar firma
        $expected = hash_hmac(
            'sha256',
            $type . '|' . $machineHash . '|' . $startAt . '|' . $endAt,
            self::MASTER_KEY
        );

        if (!hash_equals($expected, $sig)) {
            return [
                'success' => false,
                'message' => 'Firma de licencia inválida'
            ];
        }

        // Validar hardware
        if ($machineHash !== $this->machineHash()) {
            return [
                'success' => false,
                'message' => 'Esta licencia no es válida para este equipo'
            ];
        }

        // TODO OK - Ahora sí guardar
        $directory = dirname($this->path);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        file_put_contents($this->path, $raw);

        // Limpiar caché
        $this->refresh();

        // Validar estado final
        $status = $this->checkLicenseStatus();

        return [
            'success' => true,
            'message' => 'Licencia instalada correctamente',
            'status' => $status
        ];
        
    } catch (\Exception $e) {
        return [
            'success' => false,
            'message' => 'Error al instalar licencia: ' . $e->getMessage()
        ];
    }
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

        $key    = hash('sha256', self::MASTER_KEY, true);
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
            self::MASTER_KEY
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
        $status = $this->checkLicenseStatus();

        $data = [
            'status' => $status,
            'start_at' => null,
            'end_at' => null,
            'days_remaining' => null,
            'type' => null,
            'show_notification' => false,
        ];

        if (file_exists($this->path)) {
            $raw = @file_get_contents($this->path);
            if ($raw && strlen($raw) > 16) {
                $key    = hash('sha256', self::MASTER_KEY, true);
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
                        $data['type'] = $parts[0];
                        
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
     * Machine hash con caché - SINCRONIZADO CON CONTROLADOR
     */
/**
 * Machine hash con caché - MEJORADO CON UUID + DISCO + MAC
 */
private function machineHash(): string
{
    return Cache::remember('license_machine_hash', 604800, function () {
        $components = [];
        $hostname = gethostname() ?: 'unknown';
        
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // === WINDOWS ===
            
            // 1. UUID de la placa base (MÁS ESTABLE)
            $uuid = @trim(shell_exec('wmic csproduct get uuid 2>nul'));
            if (!empty($uuid)) {
                // Limpiar la salida (viene con "UUID" como header)
                $lines = array_filter(explode("\n", $uuid));
                foreach ($lines as $line) {
                    $line = trim($line);
                    if ($line !== 'UUID' && !empty($line) && $line !== 'FFFFFFFF-FFFF-FFFF-FFFF-FFFFFFFFFFFF') {
                        $components[] = str_replace([' ', '-'], '', $line);
                        break;
                    }
                }
            }
            
            // 2. Serial del disco principal
            $diskSerial = @trim(shell_exec('wmic diskdrive where "DeviceID=\'\\\\.\\PHYSICALDRIVE0\'" get serialnumber 2>nul'));
            if (!empty($diskSerial)) {
                $lines = array_filter(explode("\n", $diskSerial));
                foreach ($lines as $line) {
                    $line = trim($line);
                    if ($line !== 'SerialNumber' && !empty($line)) {
                        $components[] = preg_replace('/\s+/', '', $line);
                        break;
                    }
                }
            }
            
            // 3. MAC física (con ordenamiento para estabilidad)
            exec('getmac /fo csv /nh', $output);
            $physicalMacs = [];
            
            if (!empty($output)) {
                foreach ($output as $line) {
                    if (preg_match('/([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})/', $line, $matches)) {
                        $mac = strtoupper(str_replace('-', ':', $matches[0]));
                        
                        // Excluir MACs virtuales conocidas
                        $isVirtual = preg_match('/^(00[:-]05[:-]69|00[:-]0C[:-]29|00[:-]50[:-]56|00[:-]1C[:-]42|00[:-]15[:-]5D)/i', $mac);
                        
                        if (!$isVirtual && $mac !== '00:00:00:00:00:00') {
                            $physicalMacs[] = $mac;
                        }
                    }
                }
            }
            
            if (!empty($physicalMacs)) {
                sort($physicalMacs); // ORDENAR para consistencia
                $components[] = $physicalMacs[0];
            }
            
        } else {
            // === LINUX/UNIX ===
            
            // 1. Machine ID (único del sistema)
            $machineId = @file_get_contents('/etc/machine-id');
            if ($machineId === false) {
                $machineId = @file_get_contents('/var/lib/dbus/machine-id');
            }
            if ($machineId !== false && !empty(trim($machineId))) {
                $components[] = trim($machineId);
            }
            
            // 2. MAC física con ordenamiento
            exec("ip link show | grep 'link/ether' | awk '{print \$2}' | grep -v '00:00:00:00:00:00' | sort", $macs);
            
            if (empty($macs)) {
                exec("ifconfig | grep -o -E '([[:xdigit:]]{1,2}:){5}[[:xdigit:]]{1,2}' | grep -v '00:00:00:00:00:00' | sort", $macs);
            }
            
            if (!empty($macs) && is_array($macs)) {
                $components[] = strtoupper(trim($macs[0]));
            }
            
            // 3. UUID del filesystem raíz
            $rootUuid = @trim(shell_exec("blkid -s UUID -o value $(df / | tail -1 | awk '{print \$1}') 2>/dev/null"));
            if (!empty($rootUuid)) {
                $components[] = $rootUuid;
            }
        }
        
        // FALLBACK: Si no se obtuvo NADA (muy raro)
        if (empty($components)) {
            // Usar combinación de datos del servidor como último recurso
            $fallback = md5(
                php_uname('n') . 
                ($_SERVER['DOCUMENT_ROOT'] ?? '') . 
                ($_SERVER['SERVER_NAME'] ?? 'localhost')
            );
            $components[] = $fallback;
        }
        
        // Siempre agregar hostname al final
        $components[] = $hostname;
        
        // Generar fingerprint final
        $fingerprint = implode('|', $components);
        
        return hash('sha256', $fingerprint . 'FIXED_SALT');
    });
}
}