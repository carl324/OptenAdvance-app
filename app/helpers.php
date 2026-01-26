<?php

if (!function_exists('activeRoute')) {
    /**
     * Retorna la clase 'active' si la ruta actual coincide con las rutas proporcionadas.
     *
     * @param string|array $routes Una o varias rutas nombradas (ej: 'ventas.index' o ['ventas.*', 'ventas.show'])
     * @param string $activeClass La clase CSS a retornar si la ruta coincide (default: 'active')
     * @return string La clase 'active' si coincide, cadena vacía en caso contrario
     *
     * @example
     * activeRoute('ventas.index')
     * activeRoute(['ventas.index', 'ventas.show'])
     * activeRoute('ventas.*')
     */
    function activeRoute($routes, $activeClass = 'active')
    {
        // Convertir a array si es string
        $routes = is_array($routes) ? $routes : [$routes];

        // Verificar si alguna ruta coincide
        foreach ($routes as $route) {
            if (request()->routeIs($route)) {
                return $activeClass;
            }
        }

        return '';
    }
}

if (!function_exists('formatoHoraInteligente')) {
    /**
     * Presentación de hora inteligente para la UI (usa Carbon y translatedFormat)
     * - Hoy: mostrar solo la hora (9:24 PM)
     * - Ayer: "Ayer 9:24 PM"
     * - Últimos 7 días: "Sábado 9:24 PM"
     * - >7 días: "13 Ene 2026, 9:24 PM"
     * Retorna null si $datetime es null o inválido.
     *
     * @param \Carbon\Carbon|string|null $datetime
     * @return string|null
     */
    function formatoHoraInteligente($datetime)
    {
        if (is_null($datetime) || $datetime === '') {
            return null;
        }

        try {
            $dt = $datetime instanceof \Carbon\Carbon
                ? $datetime
                : \Carbon\Carbon::parse($datetime);

            // Asegurar locale español para translatedFormat
            \Carbon\Carbon::setLocale(config('app.locale', 'es') ?: 'es');

            $now = \Carbon\Carbon::now($dt->getTimezone());

            // Hoy
            if ($dt->isSameDay($now)) {
                return $dt->translatedFormat('g:i A');
            }

            // Ayer
            if ($dt->isSameDay($now->copy()->subDay())) {
                return 'Ayer ' . $dt->translatedFormat('g:i A');
            }

            // Últimos 7 días (excluye hoy y ayer por las comprobaciones anteriores)
            if ($dt->greaterThanOrEqualTo($now->copy()->subDays(7))) {
                // Asegurar capitalización de día (ej: "Sábado 9:24 PM")
                $dia = $dt->translatedFormat('l');
                $diaCap = mb_strtoupper(mb_substr($dia, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr($dia, 1, null, 'UTF-8');
                return $diaCap . ' ' . $dt->translatedFormat('g:i A');
            }

            // Mayor a 7 días
            return $dt->translatedFormat('d M Y, g:i A');
        } catch (\Exception $e) {
            return null;
        }
    }
}
