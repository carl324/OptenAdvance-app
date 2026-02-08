<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * @var array
     */
    protected $middleware = [
        // global middleware (kept minimal)
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            // web middleware group
        ],
    ];

    /**
     * The application's middleware aliases.
     *
     * @var array
     */
    protected $middlewareAliases = [  // ← CAMBIA ESTO
        
        'role' => \App\Http\Middleware\RoleMiddleware::class,
        'check.license' => \App\Http\Middleware\CheckLicense::class,
        // otros middlewares
    ];
}