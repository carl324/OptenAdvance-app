<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class EnsureAdminExists
{
    public function handle(Request $request, Closure $next)
    {
        // SOLO excluir setup y páginas legales
        if ($request->is('setup') || 
            $request->is('setup/*') || 
            $request->is('terminos-y-condiciones') || 
            $request->is('politica-de-privacidad')) {
            return $next($request);
        }

        // Verificar si existe al menos un admin
        $adminExists = User::where('role', 'admin')->exists();

        if (!$adminExists) {
            return redirect()->route('setup.show');
        }

        return $next($request);
    }
}