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
