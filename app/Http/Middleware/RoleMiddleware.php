<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        if (!$user) {
            abort(403, 'Acceso denegado');
        }

        $normalizedRoles = [];
        foreach ($roles as $roleGroup) {
            foreach (explode(',', (string) $roleGroup) as $role) {
                $role = strtolower(trim($role));
                if ($role !== '') {
                    $normalizedRoles[] = $role;
                }
            }
        }

        $allowedRoles = array_unique($normalizedRoles);
        $userRole = strtolower(trim((string) $user->role));

        if (!in_array($userRole, $allowedRoles, true)) {
            abort(403, 'Acceso denegado');
        }

        return $next($request);
    }
}
