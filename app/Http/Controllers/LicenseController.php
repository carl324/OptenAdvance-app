<?php
namespace App\Http\Controllers;

use App\Services\LicenseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class LicenseController extends Controller
{
    /**
     * Obtener el machine hash del servidor actual
     */
    public function showModal()
    {
        try {
            $data = app(LicenseService::class)->uiData();
            return view('modals.license', compact('data'));
        } catch (\Exception $e) {
            Log::error('Error en showModal de licencia', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return view('modals.license', ['data' => []]);
        }
    }

    public function getMachineHash()
    {
        try {
            $hash = Cache::remember('license_machine_hash', 604800, function () {
                return $this->generateMachineHash();
            });

            return response()->json([
                'success' => true,
                'machine_hash' => $hash
            ]);
        } catch (\Exception $e) {
            Log::error('Error al generar machine hash', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Error al generar hash: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Forzar regeneración del machine hash
     */
    public function refreshMachineHash()
    {
        try {
            Cache::forget('license_machine_hash');

            $hash = $this->generateMachineHash();

            Cache::put('license_machine_hash', $hash, 604800);

            return response()->json([
                'success' => true,
                'machine_hash' => $hash
            ]);
        } catch (\Exception $e) {
            Log::error('Error al regenerar machine hash', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Error al regenerar hash: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🆕 Subir y verificar archivo de licencia
     */
    public function uploadLicense(Request $request, LicenseService $licenseService)
{
    try {
        $request->validate([
            'license_file' => [
                'required',
                'file',
                'max:1024',
                function ($attribute, $value, $fail) {
                    $extension = strtolower($value->getClientOriginalExtension());
                    if ($extension !== 'lic') {
                        $fail('El archivo debe tener extensión .lic');
                    }
                }
            ]
        ]);

        $result = $licenseService->install($request->file('license_file'));

        return response()->json($result);
    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::warning('Validación fallida al subir licencia', [
            'errors' => $e->errors()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Archivo de licencia no válido. Debe ser un archivo .lic'
        ], 422);
    } catch (\Exception $e) {
        Log::error('Error al subir licencia', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Error al procesar la licencia: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * 🆕 Obtener datos de la licencia actual
     */
    public function getLicenseData(LicenseService $licenseService)
    {
        try {
            $data = $licenseService->uiData();
            
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            Log::error('Error al obtener datos de licencia', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener datos de licencia'
            ], 500);
        }
    }

    /**
     * Refrescar estado de la licencia
     */
    public function refreshLicense(LicenseService $licenseService)
    {
        try {
            // 1. Limpiar caché de licencia específico
            $licenseService->refresh();
            
            // 2. Limpiar caché general de la aplicación
            Cache::flush();
            
            // 3. Limpiar cachés de Laravel (opcional pero recomendado)
            Artisan::call('config:clear');
            Artisan::call('view:clear');
            
            // 4. Obtener datos frescos
            $data = $licenseService->uiData();
            
            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'Sistema actualizado correctamente'
            ]);
        } catch (\Exception $e) {
            Log::error('Error al refrescar licencia', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
 * Generar machine hash
 */
private function generateMachineHash(): string
{
    $components = [];
    $hostname = gethostname() ?: 'unknown';
    
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        // === WINDOWS ===
        
        // 1. UUID de la placa base (MÁS ESTABLE)
        $uuid = @trim(shell_exec('wmic csproduct get uuid 2>nul'));
        if (!empty($uuid)) {
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
                    
                    $isVirtual = preg_match('/^(00[:-]05[:-]69|00[:-]0C[:-]29|00[:-]50[:-]56|00[:-]1C[:-]42|00[:-]15[:-]5D)/i', $mac);
                    
                    if (!$isVirtual && $mac !== '00:00:00:00:00:00') {
                        $physicalMacs[] = $mac;
                    }
                }
            }
        }
        
        if (!empty($physicalMacs)) {
            sort($physicalMacs);
            $components[] = $physicalMacs[0];
        }
        
    } else {
        // === LINUX/UNIX ===
        
        // 1. Machine ID
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
    
    // FALLBACK
    if (empty($components)) {
        $fallback = md5(
            php_uname('n') . 
            ($_SERVER['DOCUMENT_ROOT'] ?? '') . 
            ($_SERVER['SERVER_NAME'] ?? 'localhost')
        );
        $components[] = $fallback;
    }
    
    // Siempre agregar hostname
    $components[] = $hostname;
    
    // Generar fingerprint final
    $fingerprint = implode('|', $components);
    
    return hash('sha256', $fingerprint . 'FIXED_SALT');
}
}