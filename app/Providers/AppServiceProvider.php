<?php

namespace App\Providers;

use App\Models\Caja;
use App\Models\Venta;
use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

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
        View::composer('layouts.app', function ($view) {
            $cajaActual = Caja::where('estado', 'abierta')->first();
            $cajaAbierta = (bool) $cajaActual;
            // Pasar la fecha completa al view (Carbon) para que la presentación
            // sea responsabilidad de la capa de vista / helpers.
            $cajaHoraApertura = $cajaActual
                ? Carbon::parse($cajaActual->fecha_apertura)
                : null;

            $ventasHoy = null;
            $ingresosHoy = null;

            if ($cajaActual) {
                $ventasQuery = Venta::where('caja_id', $cajaActual->id)
                    ->whereNotIn('estado', ['anulada', 'cancelada']);

                $ventasHoy = $ventasQuery->count();
                $ingresosHoy = (float) $ventasQuery->sum('total');
            }

            $view->with(compact('cajaActual', 'cajaAbierta', 'cajaHoraApertura', 'ventasHoy', 'ingresosHoy'));
        });
    }
}
