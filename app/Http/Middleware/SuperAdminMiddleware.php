<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            return redirect()->route('superadmin.login');
        }

        if ($request->user()->role !== 'super_admin') {
            abort(403);
        }

        // Verificar si la sesión expiró (10 minutos)
        $loginTime = $request->session()->get('super_admin_login_time');
        if (!$loginTime || $loginTime->diffInMinutes(now()) > 10) {
            Auth::logout();
            $request->session()->invalidate();
            return redirect()->route('superadmin.login')
                ->with('error', 'Sesión expirada por inactividad');
        }

        // Renovar tiempo
        $request->session()->put('super_admin_login_time', now());

        return $next($request);
    }
}