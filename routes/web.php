<?php

use App\Http\Controllers\ProductoController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\BackupController;

Route::get('/ventas', [VentaController::class, 'index'])->name('ventas.index');
Route::get('/ventas/nueva', [VentaController::class, 'create'])->name('ventas.create');
Route::post('/ventas', [VentaController::class, 'store'])->name('ventas.store');

Route::get('/ventas/{venta}', [VentaController::class, 'show'])->name('ventas.show');

// Ruta para ver la factura en nueva pestaña
Route::get('/ventas/{venta}/factura', [VentaController::class, 'factura'])->name('ventas.factura');

Route::get('/ventas/{venta}/devolucion', [VentaController::class, 'devolucion'])
    ->name('ventas.devolucion');

Route::post('/ventas/{venta}/devolucion', [VentaController::class, 'confirmarDevolucion'])
    ->name('ventas.devolucion.confirmar');

Route::get('/api/productos/buscar', [VentaController::class, 'buscarProductos'])
    ->name('productos.buscar');

// Reportes simple: vista y export
Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
Route::get('/reportes/export', [ReporteController::class, 'export'])->name('reportes.export');

Route::get('/productos/create', [ProductoController::class, 'create'])->name('productos.create');
Route::post('/productos', [ProductoController::class, 'store'])->name('productos.store');
Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');
Route::put('/productos/{id}', [ProductoController::class, 'update']);
Route::delete('/productos/{id}', [ProductoController::class, 'destroy']);

// En routes/web.php
Route::get('/reportes/ventas/{id}/detalles', [ReporteController::class, 'ventaDetalles']);
// Rutas para editar la única fila de 'empresa' (mostrar y guardar cambios)
// GET  /empresa -> mostrar formulario con datos actuales
// POST /empresa -> guardar cambios
Route::get('/empresa', [EmpresaController::class, 'edit'])->name('empresa.index');
Route::post('/empresa', [EmpresaController::class, 'update'])->name('empresa.update');

// Copia de seguridad manual: copia el archivo SQLite a la carpeta Downloads/opten-backups del usuario
Route::post('/backup', [BackupController::class, 'store'])->name('backup.store');

// Onboarding simple: vista independiente (closure que devuelve la vista)
Route::get('/onboarding', function () {
    return view('onboarding');
})->name('onboarding');
