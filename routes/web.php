<?php

use App\Http\Controllers\ProductoController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\BackupConfigController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SetupController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\PersonalController;
use App\Http\Controllers\AdminProfileController;
use App\Http\Controllers\DatabaseRestoreController;
use App\Http\Middleware\CheckLicense;
use App\Http\Controllers\LicenseController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\DevolucionController;
use App\Http\Controllers\ConfiguracionDevolucionController;

// ========== RUTAS SUPER ADMIN ==========
Route::get('/superadmin/login', [SuperAdminController::class, 'showLogin'])
    ->name('superadmin.login')
    ->middleware('guest');

Route::post('/superadmin/login', [SuperAdminController::class, 'login'])
    ->name('superadmin.login.post')
    ->middleware(['guest', 'throttle:5,1']);

Route::middleware(['auth', 'super_admin'])->group(function () {
    Route::get('/superadmin/recovery', [SuperAdminController::class, 'showRecovery'])
        ->name('superadmin.recovery');
    Route::post('/superadmin/reset-password', [SuperAdminController::class, 'resetPassword'])
        ->name('superadmin.reset.password')
        ->middleware('throttle:10,1');
    Route::post('/superadmin/logout', [SuperAdminController::class, 'logout'])
        ->name('superadmin.logout');
});

// ========== RUTAS PÚBLICAS ==========
Route::get('/setup', [SetupController::class, 'show'])->name('setup.show');
Route::post('/setup', [SetupController::class, 'store'])->name('setup.store');
Route::view('/terminos-y-condiciones', 'legal.terminos')->name('legal.terminos');
Route::view('/politica-de-privacidad', 'legal.privacidad')->name('legal.privacidad');
Route::view('/soporte/off', 'soporte.off')->name('soporte.off');

// ========== TODO LO DEMÁS REQUIERE QUE EXISTA UN ADMIN ==========
Route::middleware(['ensure.admin.exists'])->group(function () {

    // Login y logout
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Raíz
    Route::get('/', function () {
        return redirect()->route('ventas.create');
    });

    // ========== RUTAS AUTENTICADAS ==========
    Route::middleware(['auth'])->group(function () {

        // Licencia
        Route::prefix('/license')->group(function () {
            Route::get('machine-hash', [LicenseController::class, 'getMachineHash']);
            Route::post('machine-hash/refresh', [LicenseController::class, 'refreshMachineHash']);
            Route::get('data', [LicenseController::class, 'getLicenseData']);
            Route::post('upload', [LicenseController::class, 'uploadLicense']);
            Route::post('refresh', [LicenseController::class, 'refreshLicense']);
        });

        // Restauración de BD
        Route::middleware('throttle:3,10')->group(function () {
            Route::post('/database/restore', [DatabaseRestoreController::class, 'restore'])->name('database.restore');
        });

        Route::post('/superadmin/mark-revealed', [SuperAdminController::class, 'markRevealed'])
            ->name('superadmin.mark-revealed')
            ->middleware('role:admin');

        // ========== SOLO ADMIN ==========
        Route::middleware('role:admin')->group(function () {
            // Vistas

            Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
            Route::get('/base-de-datos', fn() => view('db.index'))->name('db.index');
            Route::get('/ajustes', fn() => view('ajustes.index'))->name('ajustes.index');
            Route::get('/ajustes/devoluciones', [ConfiguracionDevolucionController::class, 'devoluciones'])->name('configuracion.devoluciones');
Route::post('/ajustes/devoluciones/dias', [ConfiguracionDevolucionController::class, 'guardarDias'])->name('configuracion.dias');
Route::post('/ajustes/motivos-devolucion', [ConfiguracionDevolucionController::class, 'storeMotivoDevolucion'])->name('motivos-devolucion.store');
Route::put('/ajustes/motivos-devolucion/{motivo}', [ConfiguracionDevolucionController::class, 'updateMotivoDevolucion'])->name('motivos-devolucion.update');
Route::patch('/ajustes/motivos-devolucion/{motivo}/toggle', [ConfiguracionDevolucionController::class, 'toggleMotivoDevolucion'])->name('motivos-devolucion.toggle');
Route::delete('/ajustes/motivos-devolucion/{motivo}', [ConfiguracionDevolucionController::class, 'destroyMotivoDevolucion'])->name('motivos-devolucion.destroy');

            Route::get('/auditoria', [AuditoriaController::class, 'index'])->name('auditoria.index');
            Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');
            Route::post('/clientes', [ClienteController::class, 'store'])->name('clientes.store');
            Route::put('/clientes/{cliente}', [ClienteController::class, 'update'])->name('clientes.update');
            Route::delete('/clientes/{cliente}', [ClienteController::class, 'destroy'])->name('clientes.destroy');
            Route::get('/api/clientes/buscar', [ClienteController::class, 'buscar'])->name('clientes.buscar');
            Route::post('/clientes/{cliente}/abonar', [ClienteController::class, 'abonar'])->name('clientes.abonar');
            Route::get('/clientes/{cliente}/abonos/{abono}/comprobante', [ClienteController::class, 'comprobante'])->name('clientes.comprobante');
            Route::get('/clientes/{cliente}', [ClienteController::class, 'show'])->name('clientes.show');
            Route::get('/clientes/{cliente}/abonos', [ClienteController::class, 'listarAbonos'])->name('clientes.listarAbonos');
            Route::get('/clientes/{cliente}/abonos/print', [ClienteController::class, 'printAbonos'])->name('clientes.printAbonos');
            Route::get('/licencia', fn() => view('licencia.index'))->name('licencia.index');
            Route::get('/empresa', [EmpresaController::class, 'edit'])->name('empresa.index');
            Route::post('/empresa', [EmpresaController::class, 'update'])->name('empresa.update');
            Route::post('/empresa/logo', [EmpresaController::class, 'updateLogo'])->name('empresa.logo');
            Route::get('/personal', [PersonalController::class, 'index'])->name('personal.index');
            Route::post('/personal', [PersonalController::class, 'store'])->name('personal.store');
            Route::post('/empleados/{id}/update', [PersonalController::class, 'update']);
            Route::delete('/empleados/{id}/delete', [PersonalController::class, 'destroy']);
            Route::post('/perfil/admin/update', [PersonalController::class, 'updateAdminProfile'])->name('perfil.admin.update');

            // Reportes
            Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
            Route::get('/reportes/export', [ReporteController::class, 'export'])->name('reportes.export');
            Route::get('/api/reportes/kpis', [ReporteController::class, 'apiKpis'])->name('reportes.api.kpis');
            Route::get('/api/reportes/tendencia', [ReporteController::class, 'apiTendencia'])->name('reportes.api.tendencia');
            Route::get('/api/reportes/cajeros', [ReporteController::class, 'apiCajeros'])->name('reportes.api.cajeros');
            Route::get('/api/reportes/productos', [ReporteController::class, 'apiProductos'])->name('reportes.api.productos');
            Route::get('/api/reportes/export', [ReporteController::class, 'apiExport'])->name('reportes.api.export');
            Route::get('/reportes/ventas/{id}/detalles', [ReporteController::class, 'ventaDetalles']);

            // Backup config
            Route::get('/backup-config/obtener', [BackupConfigController::class, 'obtener']);
            Route::post('/backup-config/guardar', [BackupConfigController::class, 'guardar']);

            // Notificaciones
            Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
            Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
            Route::delete('/notifications', [NotificationController::class, 'destroyAll'])->name('notifications.destroyAll');

            // API para el contador de la campana
            Route::get('/api/notifications/count', [NotificationController::class, 'count'])->name('notifications.count');

            // Backup con licencia
            Route::middleware(CheckLicense::class)->group(function () {
                Route::post('/backup', [BackupController::class, 'store'])->name('backup.store');
            });
        });

        // ========== ADMIN Y EMPLEADO ==========
        Route::middleware(['role:admin,empleado', CheckLicense::class])->group(function () {
            // Caja
            Route::post('/caja/abrir', [CajaController::class, 'abrir'])->name('caja.abrir');
            Route::post('/caja/cerrar', [CajaController::class, 'cerrar'])->name('caja.cerrar');
            Route::get('/caja/cierre/resumen', [CajaController::class, 'resumenCierre'])->name('caja.cierre.resumen');
            Route::get('/caja/cierre/print/{caja}', [CajaController::class, 'printCierre'])->name('caja.cierre.print');
// Devoluciones

Route::get('/ventas/{venta}/devolucion', [DevolucionController::class, 'create'])->name('ventas.devolucion');
Route::post('/ventas/{venta}/devolucion', [DevolucionController::class, 'store'])->name('ventas.devolucion.store');
// Ventas
            Route::get('/ventas', [VentaController::class, 'index'])->name('ventas.index');
            Route::get('/ventas/nueva', [VentaController::class, 'create'])->name('ventas.create');
            Route::post('/ventas', [VentaController::class, 'store'])->name('ventas.store');
            Route::get('/ventas/{venta}', [VentaController::class, 'show'])->name('ventas.show');
            Route::get('/ventas/{venta}/detalle', [VentaController::class, 'detalle'])->name('ventas.detalle');
            Route::get('/ventas/{venta}/factura', [VentaController::class, 'factura'])->name('ventas.factura');
            Route::get('/ventas/{venta}/factura/pdf', [VentaController::class, 'descargarPDF'])->name('ventas.factura.pdf');
            Route::get('/ventas/{venta}/factura/impresion', [VentaController::class, 'impresion'])->name('ventas.factura.impresion');
Route::post('/ventas/{venta}/anular', [VentaController::class, 'confirmarDevolucion'])->name('ventas.devolucion.confirmar-anulacion');
            // Productos
            Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');
            Route::get('/api/productos', [VentaController::class, 'obtenerTodosProductos'])->name('productos.todos');
            Route::get('/api/productos/buscar', [VentaController::class, 'buscarProductos'])->name('productos.buscar');
            Route::post('/productos', [ProductoController::class, 'store'])->name('productos.store');
            Route::put('/productos/{id}', [ProductoController::class, 'update']);
            Route::delete('/productos/{id}', [ProductoController::class, 'destroy']);
        });

        // Rutas sin licencia requerida
        Route::middleware('role:admin,empleado')->group(function () {
            Route::get('/onboarding', fn() => view('onboarding'))->name('onboarding');
            Route::get('/soporte', fn() => view('soporte.index'))->name('soporte.index');
        });
    });
});