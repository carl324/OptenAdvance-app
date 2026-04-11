<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use App\Models\InventarioMovimiento;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Traits\Auditable;

class ProductoController extends Controller
{
    use Auditable;
    // Listar productos activos
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        
        // Búsqueda server-side con when()
        $productos = Producto::activos()
            ->when($search, function ($query, $search) {
    return $query->where(function($q) use ($search) {
        $q->where('nombre', 'LIKE', '%' . $search . '%')
          ->orWhere('codigo_barras', $search);
    });
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
    ->limit(100)
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
            'precio_compra' => 'required|numeric|gte:0',
            'precio_venta' => 'required|numeric|gt:0',
            'stock'  => 'required|numeric|min:0',
            'unidad' => 'required|string|in:Unidad,Par,Docena,Caja,Paquete,Sobre,Frasco,Botella,Lata,Tubo,Gramo,Kilogramo,Libra,Tonelada,Onza,Mililitro,Litro,Galón,Metro cúbico,Milímetro,Centímetro,Metro,Metro lineal,Kilómetro,Pulgada,Pie,Metro cuadrado,Centímetro cuadrado,Hectárea',
            'iva'          => 'required|numeric|min:0|max:100',
         
    'codigo_barras' => 'nullable|string|max:50|unique:productos,codigo_barras',
], [
    'codigo_barras.unique' => 'Este código de barras ya está registrado en otro producto.',
]);

        
        // Normalizar nombre
        $data['nombre'] = trim(mb_strtolower($data['nombre']));

        // Calcular precio con IVA
        $data['precio_con_iva'] = (int) round($data['precio_venta'] * (1 + ($data['iva'] / 100)));


        // Evitar duplicados activos
        $existe = Producto::where('nombre', $data['nombre'])
            ->where('activo', 1)
            ->exists();

        if ($existe) {
            $productoExistente = Producto::where('nombre', $data['nombre'])
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
    'nombre'        => $data['nombre'],
    'codigo_barras' => $data['codigo_barras'] ?? null,
    'precio_compra' => $data['precio_compra'],
    'precio_venta'  => $data['precio_venta'],
    'iva'           => $data['iva'],
    'precio_con_iva'=> $data['precio_con_iva'],
    'stock'         => $stockInicial,
    'unidad'        => $data['unidad'],
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
            $data = $request->validate([
                'nombre'        => 'sometimes|string|max:100',
                'precio_compra' => 'sometimes|numeric|gte:0',
                'precio_venta'  => 'sometimes|numeric|gt:0',
                'iva'           => 'sometimes|numeric|min:0|max:100',
                'stock'         => 'sometimes|numeric|min:0',
                'unidad'        => 'sometimes|string|in:Unidad,Par,Docena,Caja,Paquete,Sobre,Frasco,Botella,Lata,Tubo,Gramo,Kilogramo,Libra,Tonelada,Onza,Mililitro,Litro,Galón,Metro cúbico,Milímetro,Centímetro,Metro,Metro lineal,Kilómetro,Pulgada,Pie,Metro cuadrado,Centímetro cuadrado,Hectárea',
                'codigo_barras' => 'sometimes|nullable|string|max:50|unique:productos,codigo_barras,' . $id,
            ]);

            $producto = Producto::findOrFail($id);

            if (isset($data['nombre'])) {
                $nombreNormalizado = trim(mb_strtolower($data['nombre']));
                $existe = Producto::where('nombre', $nombreNormalizado)
                    ->where('id', '!=', $producto->id)
                    ->where('activo', 1)
                    ->exists();
                if ($existe) {
                    return response()->json(['success' => false, 'message' => 'Ya existe otro producto con ese nombre'], 409);
                }
            }

            DB::beginTransaction();

            $producto = Producto::where('id', $producto->id)->lockForUpdate()->first();
            $stockAnterior = $producto->stock;

            // Snapshot antes — solo campos que pueden cambiar
            $antes = [
                'nombre'       => $producto->nombre,
                'precio_venta' => $producto->precio_venta,
                'precio_compra'=> $producto->precio_compra,
                'iva'          => $producto->iva,
                'stock'        => $producto->stock,
            ];

            // Detectar qué cambió para auditoría específica
            $cambioNombre  = isset($data['nombre'])        && $data['nombre'] !== $producto->nombre;
            $cambioPrecio  = isset($data['precio_venta'])  && (float)$data['precio_venta'] !== (float)$producto->precio_venta;
            $cambioStock   = isset($data['stock'])         && (float)$data['stock'] !== (float)$producto->stock;
            $cambioUnidad  = isset($data['unidad'])        && $data['unidad'] !== $producto->unidad;

            if (isset($data['nombre']))        $producto->nombre        = $data['nombre'];
            if (isset($data['precio_compra'])) $producto->precio_compra = $data['precio_compra'];
            if (isset($data['precio_venta']))  $producto->precio_venta  = $data['precio_venta'];
            if (isset($data['iva']))           $producto->iva           = $data['iva'];
            if (array_key_exists('codigo_barras', $data)) $producto->codigo_barras = $data['codigo_barras'];
            if (isset($data['unidad'])) $producto->unidad = $data['unidad'];

            if (isset($data['precio_venta']) || isset($data['iva'])) {
                $producto->precio_con_iva = (int) round($producto->precio_venta * (1 + ($producto->iva / 100)));
            }

            $producto->save();

            if (isset($data['stock'])) {
                $stockNuevo = (float) $data['stock'];
                $diferencia  = $stockNuevo - $stockAnterior;

                if ($stockNuevo < 0) throw new \Exception("El stock no puede ser negativo");

                if (abs($diferencia) > 0.001) {
                    if ($diferencia > 0) {
                        InventarioMovimiento::entrada($producto->id, abs($diferencia), 'ajuste', $producto->id, "Ajuste manual: de {$stockAnterior} a {$stockNuevo}", Auth::id());
                    } else {
                        InventarioMovimiento::salida($producto->id, abs($diferencia), 'ajuste', $producto->id, "Ajuste manual: de {$stockAnterior} a {$stockNuevo}", Auth::id());
                    }
                    $producto->refresh();
                }
            }

            $despues = [
                'nombre'       => $producto->nombre,
                'precio_venta' => $producto->precio_venta,
                'precio_compra'=> $producto->precio_compra,
                'iva'          => $producto->iva,
                'stock'        => $producto->stock,
            ];

            // ── Auditoría: una entrada por tipo de cambio detectado ──
            if ($cambioNombre) {
                self::registrar('cambio_nombre_producto', 'producto', $producto->id,
                    ['nombre' => $antes['nombre']],
                    ['nombre' => $despues['nombre']],
                    "Nombre cambiado de '{$antes['nombre']}' a '{$despues['nombre']}'"
                );
            }

            if ($cambioPrecio) {
                self::registrar('cambio_precio_producto', 'producto', $producto->id,
                    ['precio_venta' => $antes['precio_venta']],
                    ['precio_venta' => $despues['precio_venta']],
                    "Precio cambiado de \${$antes['precio_venta']} a \${$despues['precio_venta']}"
                );
            }

            if ($cambioStock) {
                self::registrar('ajuste_inventario', 'producto', $producto->id,
                    ['stock' => $antes['stock']],
                    ['stock' => $despues['stock']],
                    "Stock ajustado de {$antes['stock']} a {$despues['stock']}"
                );
            }

            DB::commit();

            return response()->json(['success' => true, 'producto' => $producto->fresh()]);

        } catch (\Illuminate\Validation\ValidationException $e) {
    return response()->json([
        'success' => false, 
        'message' => $e->errors()['nombre'][0] ?? $e->errors()['precio_venta'][0] ?? 'Datos inválidos',
        'errors' => $e->errors()
    ], 422);
} catch (\Throwable $e) {
            DB::rollBack();
            Log::error('ERROR EN UPDATE: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'No se pudo actualizar el producto'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $producto = Producto::findOrFail($id);

            // Snapshot antes de eliminar
            $antes = [
                'nombre'       => $producto->nombre,
                'precio_venta' => $producto->precio_venta,
                'stock'        => $producto->stock,
                'activo'       => 1,
            ];

            $producto->update(['activo' => 0]);

            // ── Auditoría ──
            self::registrar(
                'eliminacion_producto',
                'producto',
                $producto->id,
                $antes,
                ['activo' => 0],
                "Producto '{$producto->nombre}' desactivado"
            );

            return response()->json(['success' => true]);

        } catch (\Throwable $e) {
            Log::error('Error al eliminar producto: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'No se pudo eliminar el producto'], 500);
        }
    }
}