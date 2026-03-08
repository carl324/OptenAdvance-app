<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\LicenseService;

class CheckLicense
{
    /**
     * Maneja la solicitud entrante.
     */
    public function handle(Request $request, Closure $next)
    {
        $licenseService = app(LicenseService::class);
        $status = $licenseService->status();

        // Estados bloqueantes
        if (in_array($status, ['expired'])) {
            // Si es una petición AJAX/fetch
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'error' => 'Licencia vencida',
                    'show_modal' => true
                ], 403);
            }
            
            // Si es una petición normal de formulario
            return back()->with('license_expired', true);
        }

        return $next($request);
    }
}