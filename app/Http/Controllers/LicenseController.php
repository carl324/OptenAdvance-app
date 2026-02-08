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
    $hostname = gethostname() ?: 'unknown';
    
    $mac = '';
    
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        // NUEVO: Obtener todas las líneas de getmac
        exec('getmac', $output);
        
        // Buscar la primera MAC válida
        foreach ($output as $line) {
            if (preg_match('/([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})/', $line, $matches)) {
                $mac = $matches[0];
                break;
            }
        }
    } else {
        $mac = @exec("ip link show | grep ether | awk '{print $2}' | head -n 1");
        
        if (empty($mac)) {
            $mac = @exec("ifconfig | grep -o -E '([[:xdigit:]]{1,2}:){5}[[:xdigit:]]{1,2}' | head -n 1");
        }
    }
    
    $mac = preg_replace('/\s+/', '', $mac ?? '');
    
    if (empty($mac)) {
        $mac = $_SERVER['SERVER_ADDR'] ?? '127.0.0.1';
    }

    return hash('sha256', $mac . $hostname . 'FIXED_SALT');
}
}