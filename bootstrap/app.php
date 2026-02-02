<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Agregar middleware al grupo 'web'
        $middleware->web(append: [
            \App\Http\Middleware\CheckLicenseNotification::class,
        ]);
        
        // Aliases de middlewares
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'check.license' => \App\Http\Middleware\CheckLicense::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
