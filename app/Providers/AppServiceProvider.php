<?php

namespace App\Providers;

use App\Models\Caja;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use App\Services\LicenseService;

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
        // ✅ ESPECÍFICO: Solo inyectar datos de licencia en vistas que los usan
        View::composer(['layouts.app', 'modals.license'], function ($view) {
    $licenseData = Cache::remember('app_license_data', 600, function () {
        return app(LicenseService::class)->uiData();
    });

    $view->with([
        'data' => $licenseData,
        'licenseNotification' => null,
    ]);
});
        // ✅ OPTIMIZADO: Composer específico para layouts.app con cache
        View::composer('layouts.app', function ($view) {
            // Cache de datos de caja (1 minuto - se actualiza frecuentemente)
            $cajaActual = Caja::where('estado', 'abierta')->first();

$ventasHoy = null;
$ingresosHoy = null;

if ($cajaActual) {
    $stats = Venta::where('caja_id', $cajaActual->id)
        ->whereNotIn('estado', ['anulada', 'cancelada', 'devuelta'])
        ->selectRaw('
            COUNT(*) as count,
            COALESCE(SUM(
                CASE
                    WHEN estado = "completada" THEN total
                    WHEN estado IN ("credito", "parcial") THEN total - saldo_pendiente
                    WHEN estado = "dev_parcial" THEN total - COALESCE((
                        SELECT SUM(monto_real) FROM devoluciones WHERE venta_id = ventas.id
                    ), 0)
                    ELSE 0
                END
            ), 0) as ingresos
        ')
        ->first();

    $ventasHoy  = $stats->count ?? 0;
    $ingresosHoy = (float)($stats->ingresos ?? 0);
}

           
            
            $cajaAbierta = (bool) $cajaActual;
            $cajaHoraApertura = $cajaActual 
                ? Carbon::parse($cajaActual->fecha_apertura)
                : null;
            
            $view->with(compact('cajaActual', 'cajaAbierta', 'cajaHoraApertura', 'ventasHoy', 'ingresosHoy'));
        });
    }
}