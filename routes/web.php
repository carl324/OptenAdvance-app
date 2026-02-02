<?php

use App\Http\Controllers\ProductoController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SetupController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\PersonalController;
use App\Http\Controllers\AdminProfileController;
use App\Http\Controllers\DatabaseRestoreController;
use App\Http\Middleware\CheckLicense;
//use App\Http\Controllers\BackupConfigController;

//Route::prefix('backup-config')->middleware('auth')->group(function() { Route::get('/carpetas', [App\Http\Controllers\BackupConfigController::class, 'listarCarpetas'])->name('backup.carpetas'); Route::get('/obtener', [App\Http\Controllers\BackupConfigController::class, 'obtener'])->name('backup.obtener'); Route::post('/guardar', [App\Http\Controllers\BackupConfigController::class, 'guardar'])->name('backup.guardar'); });


Route::middleware(['auth', 'throttle:3,10'])->group(function () {
    Route::post('/database/restore', [DatabaseRestoreController::class, 'restore'])->name('database.restore');
});
Route::get('/setup', [SetupController::class, 'show'])->name('setup.show');
Route::post('/setup', [SetupController::class, 'store'])->name('setup.store');
Route::view('/terminos-y-condiciones', 'legal.terminos')->name('legal.terminos');
Route::view('/politica-de-privacidad', 'legal.privacidad')->name('legal.privacidad');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {

    // Rutas bloqueables por licencia
    Route::middleware(['role:admin,empleado', CheckLicense::class])->group(function () {
        Route::post('/caja/abrir', [CajaController::class, 'abrir'])->name('caja.abrir');
        Route::post('/caja/cerrar', [CajaController::class, 'cerrar'])->name('caja.cerrar');

        Route::post('/ventas', [VentaController::class, 'store'])->name('ventas.store');
        Route::post('/ventas/{venta}/devolucion', [VentaController::class, 'confirmarDevolucion'])->name('ventas.devolucion.confirmar');

        Route::post('/productos', [ProductoController::class, 'store'])->name('productos.store');
        Route::put('/productos/{id}', [ProductoController::class, 'update']);
        Route::delete('/productos/{id}', [ProductoController::class, 'destroy']);

        Route::post('/backup', [BackupController::class, 'store'])->name('backup.store');

        Route::post('/empleados/{id}/update', [PersonalController::class, 'update']);
        Route::delete('/empleados/{id}/delete', [PersonalController::class, 'destroy']);
        Route::post('/perfil/admin/update', [PersonalController::class, 'updateAdminProfile'])->name('perfil.admin.update');
    });

    // Rutas de solo lectura, sin bloqueo de licencia
    Route::middleware(['role:admin,empleado'])->group(function () {
        Route::get('/caja/cierre/resumen', [CajaController::class, 'resumenCierre'])->name('caja.cierre.resumen');
        Route::get('/caja/cierre/print/{caja}', [CajaController::class, 'printCierre'])->name('caja.cierre.print');
        Route::post('/personal', [PersonalController::class, 'store'])->name('personal.store');
        Route::get('/ventas', [VentaController::class, 'index'])->name('ventas.index');
        Route::get('/ventas/nueva', [VentaController::class, 'create'])->name('ventas.create');
        Route::get('/ventas/{venta}', [VentaController::class, 'show'])->name('ventas.show');
        Route::get('/ventas/{venta}/detalle', [VentaController::class, 'detalle'])->name('ventas.detalle');
        Route::get('/ventas/{venta}/factura', [VentaController::class, 'factura'])->name('ventas.factura');
        Route::get('/ventas/{venta}/factura/pdf', [VentaController::class, 'descargarPDF'])->name('ventas.factura.pdf');
        Route::get('/ventas/{venta}/factura/impresion', [VentaController::class, 'impresion'])->name('ventas.factura.impresion');
        Route::post('/empresa', [EmpresaController::class, 'update'])->name('empresa.update');
        Route::get('/api/productos', [VentaController::class, 'obtenerTodosProductos'])->name('productos.todos');
        Route::get('/api/productos/buscar', [VentaController::class, 'buscarProductos'])->name('productos.buscar');

        Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');
        Route::get('/personal', [PersonalController::class, 'index'])->name('personal.index');
        Route::get('/empresa', [EmpresaController::class, 'edit'])->name('empresa.index');

        Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
        Route::get('/reportes/export', [ReporteController::class, 'export'])->name('reportes.export');
        Route::get('/api/reportes', [ReporteController::class, 'apiData'])->name('reportes.api');
        Route::get('/api/reportes/stats', [ReporteController::class, 'apiStats'])->name('reportes.api.stats');
        Route::get('/api/reportes/export', [ReporteController::class, 'apiExport'])->name('reportes.api.export');
        Route::get('/reportes/ventas/{id}/detalles', [ReporteController::class, 'ventaDetalles']);

        Route::get('/onboarding', function () { return view('onboarding'); })->name('onboarding');
        Route::get('/soporte', function () { return view('soporte.index'); })->name('soporte.index');
    });
});
