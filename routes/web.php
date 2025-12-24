<?php

use App\Http\Controllers\ProductoController;
use App\Http\Controllers\VentaController;

Route::get('/ventas/nueva', [VentaController::class, 'create'])->name('ventas.create');
Route::post('/ventas', [VentaController::class, 'store'])->name('ventas.store');
Route::post('/ventas/{id}/anular', [VentaController::class, 'anular'])->name('ventas.anular');
Route::get('/api/productos/buscar', [VentaController::class, 'buscarProductos'])->name('productos.buscar');

Route::get('/productos/create', [ProductoController::class, 'create']);
Route::post('/productos', [ProductoController::class, 'store']);
Route::get('/productos', [ProductoController::class, 'index']);
Route::put('/productos/{id}', [ProductoController::class, 'update']);
Route::delete('/productos/{id}', [ProductoController::class, 'destroy']);
