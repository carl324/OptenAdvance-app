<?php

namespace App\Providers;

use App\Models\Caja;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use App\Services\LicenseService;
use App\Http\Controllers\LicenseNotificationController;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ✅ CONSOLIDADO: Un único composer para todas las vistas
        View::composer('*', function ($view) {
            // Cache para datos de licencia (10 minutos)
            $licenseData = Cache::remember('app_license_data', 600, function () {
                return app(LicenseService::class)->uiData();
            });
            
            // Cache para notificación de licencia (5 minutos)
            $notification = Cache::remember('app_license_notification', 300, function () {
                $licenseService = app(LicenseService::class);
                return app(LicenseNotificationController::class)->check($licenseService);
            });

            $view->with([
                'data' => $licenseData,
                'licenseNotification' => $notification,
            ]);
        });

        // ✅ OPTIMIZADO: Composer específico para layouts.app con cache
        View::composer('layouts.app', function ($view) {
            // Cache de datos de caja (1 minuto - se actualiza frecuentemente)
            $cajaData = Cache::remember('app_caja_actual_data', 60, function () {
                $cajaActual = Caja::where('estado', 'abierta')->first();
                
                $ventasHoy = null;
                $ingresosHoy = null;
                
                if ($cajaActual) {
                    // ✅ UNA SOLA QUERY con agregación en lugar de 2
                    $stats = Venta::where('caja_id', $cajaActual->id)
                        ->whereNotIn('estado', ['anulada', 'cancelada'])
                        ->selectRaw('COUNT(*) as count, COALESCE(SUM(total), 0) as ingresos')
                        ->first();
                    
                    $ventasHoy = $stats->count ?? 0;
                    $ingresosHoy = (float)($stats->ingresos ?? 0);
                }
                
                return compact('cajaActual', 'ventasHoy', 'ingresosHoy');
            });

            // Extraer datos del cache
            extract($cajaData);
            
            $cajaAbierta = (bool) $cajaActual;
            $cajaHoraApertura = $cajaActual 
                ? Carbon::parse($cajaActual->fecha_apertura)
                : null;
            
            $view->with(compact('cajaActual', 'cajaAbierta', 'cajaHoraApertura', 'ventasHoy', 'ingresosHoy'));
        });
    }
}