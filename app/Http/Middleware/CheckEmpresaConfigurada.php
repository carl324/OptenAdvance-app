<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Empresa;

class CheckEmpresaConfigurada
{
    /**
     * Handle an incoming request.
     * If no Empresa exists, redirect silently to the empresa configuration route.
     * Excepciones: rutas `empresa.*`, assets (paths como assets/*, storage/*), y rutas de error.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Middleware neutralizado: no bloquea ni redirige.
        // Se mantiene el archivo para facilitar reversiones futuras,
        // pero su responsabilidad ha sido retirada según solicitud.
        return $next($request);
    }
}
