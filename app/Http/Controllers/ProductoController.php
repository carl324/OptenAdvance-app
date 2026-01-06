<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\InventarioMovimiento;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductoController extends Controller
{
    // Listar productos activos
    public function index()
    {
        $productos = Producto::activos()
            ->orderBy('id', 'desc')
            ->get();

        $empresa = \App\Models\Empresa::first();
        // Indica si existe al menos un producto con IVA > 0 (histórico)
        $hayProductosConIVA = $productos->contains(function($p) {
            return isset($p->iva) && (float)$p->iva > 0;
        });

        return view('productos.index', compact('productos', 'empresa', 'hayProductosConIVA'));
    }

    // Vista de registro
    public function create()
    {
        $empresa = \App\Models\Empresa::first();
        return view('productos.create', compact('empresa'));
    }

    // Registrar producto + movimiento inicial (AJAX)
    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:100',
            'precio' => 'required|numeric|gt:0',
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

            // Crear producto con stock inicial en una única transacción
            // Esto garantiza que ambas operaciones (crear + registrar movimiento) son atómicas
            $producto = Producto::create([
                'nombre' => $data['nombre'],
                'precio' => $data['precio'],
                'iva' => $data['iva'],
                'precio_con_iva' => $data['precio_con_iva'],
                'stock'  => $stockInicial,  // Crear directamente con stock correcto
            ]);

            // Registrar movimiento inicial si aplica (siempre, para auditoría completa)
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
                'producto' => $producto->fresh()
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error en store producto: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error al registrar el producto'
            ], 500);
        }
    }

    // Actualizar producto (incluye stock con movimiento)
    public function update(Request $request, $id)
    {
        try {
            Log::info('=== INICIO UPDATE PRODUCTO ===');
            Log::info('ID: ' . $id);
            Log::info('Data recibida: ' . json_encode($request->all()));

            $data = $request->validate([
                'nombre' => 'sometimes|string|max:100',
                'precio' => 'sometimes|numeric|min:0',
                'iva' => 'sometimes|numeric|min:0|max:100',
                'stock' => 'sometimes|integer|min:0',
            ]);

            Log::info('Data validada: ' . json_encode($data));

            $producto = Producto::findOrFail($id);
            Log::info('Producto encontrado: ' . $producto->nombre);

            // Normalizar nombre si viene
            if (isset($data['nombre'])) {
                $nombreNormalizado = trim(mb_strtolower($data['nombre']));
                
                // Evitar duplicados al editar
                $existe = Producto::whereRaw('LOWER(nombre) = ?', [$nombreNormalizado])
                    ->where('id', '!=', $producto->id)
                    ->where('activo', 1)
                    ->exists();

                if ($existe) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Ya existe otro producto con ese nombre'
                    ], 409);
                }
            }

            DB::beginTransaction();

            // Lock de fila para prevenir race conditions en concurrencia
            $producto = Producto::where('id', $producto->id)->lockForUpdate()->first();
            $stockAnterior = $producto->stock;
            Log::info('Stock anterior: ' . $stockAnterior);

            // Actualizar campos básicos
            if (isset($data['nombre'])) {
                $producto->nombre = $data['nombre'];
            }
            if (isset($data['precio'])) {
                $producto->precio = $data['precio'];
            }
            if (isset($data['iva'])) {
                $producto->iva = $data['iva'];
            }

            // Recalcular precio con IVA si cambió precio o iva
            if (isset($data['precio']) || isset($data['iva'])) {
                $producto->precio_con_iva = $producto->precio * (1 + ($producto->iva / 100));
            }

            // Guardar cambios básicos primero
            $producto->save();
            Log::info('Producto guardado con campos básicos');

            // ✅ Manejar stock DESPUÉS de guardar lo demás
            if (isset($data['stock'])) {
                $stockNuevo = (int)$data['stock'];
                Log::info('Stock nuevo: ' . $stockNuevo);
                
                $diferencia = $stockNuevo - $stockAnterior;
                Log::info('Diferencia: ' . $diferencia);

                // Validar que no vaya a quedar en negativo
                if ($stockNuevo < 0) {
                    throw new \Exception("El stock no puede ser negativo");
                }

                if ($diferencia !== 0) {
                    // ✅ Usar entrada/salida según si aumenta o disminuye
                    if ($diferencia > 0) {
                        // Stock aumentó = ENTRADA
                        Log::info('Registrando ENTRADA de ' . abs($diferencia));
                        InventarioMovimiento::entrada(
                            $producto->id,
                            abs($diferencia),
                            'ajuste',
                            $producto->id,
                            "Ajuste manual: de {$stockAnterior} a {$stockNuevo} (+{$diferencia})"
                        );
                    } else {
                        // Stock disminuyó = SALIDA
                        Log::info('Registrando SALIDA de ' . abs($diferencia));
                        InventarioMovimiento::salida(
                            $producto->id,
                            abs($diferencia),
                            'ajuste',
                            $producto->id,
                            "Ajuste manual: de {$stockAnterior} a {$stockNuevo} ({$diferencia})"
                        );
                    }

                    // Refrescar el producto para obtener el stock actualizado
                    $producto->refresh();
                    Log::info('Stock después del movimiento: ' . $producto->stock);
                }
            }

            DB::commit();
            Log::info('=== FIN UPDATE PRODUCTO EXITOSO ===');

            return response()->json([
                'success' => true,
                'producto' => $producto->fresh()
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Error de validación: ' . json_encode($e->errors()));
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('ERROR EN UPDATE: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el producto',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Eliminación lógica
    public function destroy($id)
    {
        try {
            $producto = Producto::findOrFail($id);
            $producto->update(['activo' => 0]);

            return response()->json(['success' => true]);

        } catch (\Throwable $e) {
            Log::error('Error al eliminar producto: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el producto'
            ], 500);
        }
    }
}