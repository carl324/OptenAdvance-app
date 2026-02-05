<?php

namespace App\Http\Middleware;

use Closure;
use App\Services\LicenseService;
use App\Http\Controllers\LicenseNotificationController;
use Illuminate\Http\Request;

class CheckLicenseNotification
{
    public function handle(Request $request, Closure $next)
    {
    //    $licenseService = app(LicenseService::class);
    //    $controller = new LicenseNotificationController();
        
    //    $notification = $controller->check($licenseService);
        
        // Compartir con todas las vistas
   //     view()->share('licenseNotification', $notification);
        
    //    return $next($request);
    }
}