<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\InventarioMovimiento;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ProductoController extends Controller
{
    // Listar productos activos
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        
        // Búsqueda server-side con when()
        $productos = Producto::activos()
            ->when($search, function ($query, $search) {
                return $query->whereRaw('LOWER(nombre) LIKE ?', ['%' . strtolower($search) . '%']);
            })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->appends($request->query());

        $empresa = \App\Models\Empresa::first();

        // Obtener últimos movimientos de inventario con nombre del producto
        $movimientos = DB::table('inventario_movimientos')
            ->leftJoin('productos', 'productos.id', '=', 'inventario_movimientos.producto_id')
            ->select(
                'inventario_movimientos.id',
                'inventario_movimientos.producto_id',
                'inventario_movimientos.tipo',
                'inventario_movimientos.cantidad',
                'inventario_movimientos.origen',
                'inventario_movimientos.created_at',
                'productos.nombre as producto_nombre'
            )
            ->orderBy('inventario_movimientos.created_at', 'desc')
            ->get();

        // Si es solicitud AJAX, devolver JSON con HTML renderizado
        if ($request->ajax() || $request->wantsJson()) {
            $showActions = Auth::check() && Auth::user()->role === 'admin';

            return response()->json([
                'success' => true,
                'html' => view('productos._table', [
                    'productos' => $productos,
                    'empresa' => $empresa,
                    'showActions' => $showActions
                ])->render(),
                'pagination' => view('productos._pagination', [
                    'productos' => $productos,
                    'search' => $search
                ])->render(),
                'currentPage' => $productos->currentPage(),
                'lastPage' => $productos->lastPage(),
                'total' => $productos->total()
            ]);
        }

        return view('productos.index', compact('productos', 'empresa', 'movimientos'));
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
        $data['precio_con_iva'] = (int) round($data['precio'] * (1 + ($data['iva'] / 100)));


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

            // Registrar movimiento inicial SIEMPRE si hay stock
            $movimiento = null;
            if ($stockInicial > 0) {
                $movimiento = InventarioMovimiento::create([
                    'producto_id' => $producto->id,
                    'cantidad' => $stockInicial,
                    'tipo' => 'entrada',
                    'origen' => 'registro_producto',
                    'descripcion' => 'Stock inicial al registrar producto',
                    'user_id' => Auth::id(),
                ]);
            }

            DB::commit();

            $productoFresh = $producto->fresh();
            
            // Devolver movimiento en respuesta si existe
            $movimientoData = null;
            if ($movimiento) {
                $movimientoData = [
                    'id' => $movimiento->id,
                    'fecha' => \Carbon\Carbon::parse($movimiento->created_at)->format('d/m/Y H:i'),
                    'producto_nombre' => $productoFresh->nombre,
                    'cantidad' => $movimiento->cantidad,
                    'tipo' => $movimiento->tipo,
                    'origen' => $movimiento->origen
                ];
            }

            return response()->json([
                'success' => true,
                'producto' => $productoFresh,
                'movimiento' => $movimientoData
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Error en store producto: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'No se pudo registrar el producto'
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
                $producto->precio_con_iva = (int) round($producto->precio * (1 + ($producto->iva / 100)));

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
                            "Ajuste manual: de {$stockAnterior} a {$stockNuevo} (+{$diferencia})",
                            Auth::id()
                        );
                    } else {
                        // Stock disminuyó = SALIDA
                        Log::info('Registrando SALIDA de ' . abs($diferencia));
                        InventarioMovimiento::salida(
                            $producto->id,
                            abs($diferencia),
                            'ajuste',
                            $producto->id,
                            "Ajuste manual: de {$stockAnterior} a {$stockNuevo} ({$diferencia})",
                            Auth::id()
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
            Log::warning('Error de validación en update: ' . json_encode($e->errors()));
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos'
            ], 422);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('ERROR EN UPDATE: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'No se pudo actualizar el producto'
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
                'message' => 'No se pudo eliminar el producto'
            ], 500);
        }
    }
}