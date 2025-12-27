<?php

use App\Http\Controllers\ProductoController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\EmpresaController;

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

Route::get('/productos/create', [ProductoController::class, 'create']);
Route::post('/productos', [ProductoController::class, 'store']);
Route::get('/productos', [ProductoController::class, 'index']);
Route::put('/productos/{id}', [ProductoController::class, 'update']);
Route::delete('/productos/{id}', [ProductoController::class, 'destroy']);

// Rutas para editar la única fila de 'empresa'
Route::get('/empresa/edit', [EmpresaController::class, 'edit'])->name('empresa.edit');
Route::put('/empresa', [EmpresaController::class, 'update'])->name('empresa.update');
