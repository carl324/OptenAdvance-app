<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\Producto;
use App\Models\InventarioMovimiento;

class VentaController extends Controller
{
    // Vista del formulario
    public function create()
    {
        return view('ventas.create');
    }

    // Buscar productos (motor de búsqueda avanzado)
    // En VentaController.php - método buscarProductos()

public function buscarProductos(Request $request)
{
    $query = $request->input('q', '');

    if (strlen($query) < 2) {
        return response()->json([]);
    }

    // Búsqueda avanzada - TODOS los productos activos (incluso sin stock)
    $productos = Producto::activos()
        ->where(function($q) use ($query) {
            $q->whereRaw('LOWER(nombre) LIKE ?', ['%' . strtolower($query) . '%']);
        })
        ->select('id', 'nombre', 'precio', 'stock')
        ->orderByDesc('stock') // Productos con stock primero
        ->limit(10)
        ->get();

    return response()->json($productos);
}

    // Registrar venta
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'cliente' => 'nullable|string|max:100',
                'productos' => 'required|array|min:1',
                'productos.*.id' => 'required|exists:productos,id',
                'productos.*.cantidad' => 'required|integer|min:1',
                'productos.*.precio' => 'required|numeric|min:0',
            ]);

            // Iniciar transacción
            DB::beginTransaction();

            // Validar stock disponible para cada producto
            foreach ($data['productos'] as $item) {
                $producto = Producto::find($item['id']);
                
                if (!$producto || !$producto->activo) {
                    throw new \Exception("El producto no está disponible");
                }

                if ($producto->stock < $item['cantidad']) {
                    throw new \Exception("No hay suficiente stock de '{$producto->nombre}'. Disponible: {$producto->stock}");
                }
            }

            // Calcular total
            $total = 0;
            foreach ($data['productos'] as $item) {
                $total += $item['cantidad'] * $item['precio'];
            }

            // Crear venta
            $venta = Venta::create([
                'cliente' => $data['cliente'] ?? null,
                'total' => $total,
                'estado' => 'completada',
                'fecha' => now(),
            ]);

            // Crear detalles y descontar stock
            foreach ($data['productos'] as $item) {
                $subtotal = $item['cantidad'] * $item['precio'];

                // Crear detalle
                VentaDetalle::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $item['id'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio'],
                    'subtotal' => $subtotal,
                ]);

                // Registrar salida de inventario (descuenta stock automáticamente)
                InventarioMovimiento::salida(
                    $item['id'],
                    $item['cantidad'],
                    'venta',
                    $venta->id,
                    "Venta #{$venta->id}"
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Venta registrada correctamente',
                'venta_id' => $venta->id,
                'total' => $venta->total,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Por favor verifica los datos ingresados',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    // Anular venta
    public function anular($id)
    {
        try {
            DB::beginTransaction();

            $venta = Venta::findOrFail($id);

            if ($venta->estado === 'anulada') {
                throw new \Exception('Esta venta ya está anulada');
            }

            // Devolver stock de cada producto
            foreach ($venta->detalles as $detalle) {
                InventarioMovimiento::entrada(
                    $detalle->producto_id,
                    $detalle->cantidad,
                    'venta_anulada',
                    $venta->id,
                    "Anulación de venta #{$venta->id}"
                );
            }

            // Marcar venta como anulada
            $venta->update(['estado' => 'anulada']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Venta anulada correctamente. El stock ha sido devuelto.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}