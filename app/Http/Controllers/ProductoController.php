<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\InventarioMovimiento;
use Illuminate\Support\Facades\DB;

class ProductoController extends Controller
{
    // Listar productos activos
    public function index()
    {
        $productos = Producto::activos()
            ->orderBy('id', 'desc')
            ->get();

        return view('productos.index', compact('productos'));
    }

    // Vista de registro
    public function create()
    {
        return view('productos.create');
    }

    // Registrar producto + movimiento inicial (AJAX)
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:100',
            'precio' => 'required|numeric|min:0',
            'stock'  => 'required|integer|min:0',
            'iva'    => 'required|numeric|min:0|max:100',
        ]);

        // Normalizar nombre
        $data['nombre'] = trim(mb_strtolower($data['nombre']));

        // Calcular precio con IVA
        $data['precio_con_iva'] = $data['precio'] * (1 + ($data['iva'] / 100));

        // Evitar duplicados activos
        $existe = Producto::whereRaw('LOWER(nombre) = ?', [$data['nombre']])
            ->where('activo', 1)
            ->exists();

        if ($existe) {
            $productoExistente = Producto::whereRaw('LOWER(nombre) = ?', [$data['nombre']])
                ->where('activo', 1)
                ->first();

            return response()->json([
                'success' => false,
                'message' => 'El producto ya existe',
                'producto' => $productoExistente
            ], 409);
        }

        DB::beginTransaction();

        try {
            $stockInicial = $data['stock'];

            // Crear producto SIEMPRE con stock 0
            $producto = Producto::create([
                'nombre' => $data['nombre'],
                'precio' => $data['precio'],
                'iva' => $data['iva'],
                'precio_con_iva' => $data['precio_con_iva'],
                'stock'  => 0,
            ]);

            // Registrar movimiento inicial si aplica
            if ($stockInicial > 0) {
                InventarioMovimiento::entrada(
                    $producto->id,
                    $stockInicial,
                    'registro_producto',
                    $producto->id,
                    'Stock inicial al registrar producto'
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'producto' => $producto
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al registrar el producto'
            ], 500);
        }
    }

    // Actualizar producto (NO stock)
    public function update(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);

        $data = $request->validate([
            'nombre' => 'required|string|max:100',
            'precio' => 'required|numeric|min:0',
            'iva'    => 'required|numeric|min:0|max:100',
        ]);

        $data['nombre'] = trim(mb_strtolower($data['nombre']));

        // Calcular precio con IVA
        $data['precio_con_iva'] = $data['precio'] * (1 + ($data['iva'] / 100));

        // Evitar duplicados al editar
        $existe = Producto::whereRaw('LOWER(nombre) = ?', [$data['nombre']])
            ->where('id', '!=', $producto->id)
            ->where('activo', 1)
            ->exists();

        if ($existe) {
            return response()->json([
                'success' => false,
                'message' => 'Ya existe otro producto con ese nombre'
            ], 409);
        }

        $producto->update($data);

        return response()->json(['success' => true]);
    }

    // Eliminación lógica
    public function destroy($id)
    {
        $producto = Producto::findOrFail($id);
        $producto->update(['activo' => 0]);

        return response()->json(['success' => true]);
    }
}