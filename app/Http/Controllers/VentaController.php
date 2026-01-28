<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\Producto;
use App\Models\InventarioMovimiento;
use App\Models\Caja;
use App\Models\Empresa;

class VentaController extends Controller
{
    /**
     * Normalizar productos según configuración de IVA de empresa
     * Soluciona deuda técnica #5: evitar repetición de lógica IVA
     * @param mixed $productos Colección o resultado query de productos
     * @return mixed
     */
    private function normalizarIVA($productos)
    {
        $empresa = Empresa::first();
        $cobraIva = $empresa ? (bool) $empresa->cobra_iva : true;
        
        if (!$cobraIva) {
            $productos->transform(function ($p) {
                $p->iva = 0;
                return $p;
            });
        }
        
        return $productos;
    }

    // Vista del formulario
    public function create()
    {
        $empresa = Empresa::first();
        $cajaAbierta = Caja::where('estado', 'abierta')->exists();
        return view('ventas.create', compact('empresa', 'cajaAbierta'));
    }

    // Buscar productos (incluye IVA)
    public function buscarProductos(Request $request)
    {
        $query = $request->input('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $productos = Producto::activos()
            ->whereRaw('LOWER(nombre) LIKE ?', ['%' . strtolower($query) . '%'])
            ->select('id', 'nombre', 'precio', 'stock', 'iva')
            ->orderByDesc('stock')
            ->limit(10)
            ->get();

        return response()->json($this->normalizarIVA($productos));
    }

    // Registrar venta
    public function store(Request $request)
    {
        try {
            Log::info('Iniciando creación de venta', [
                'cliente' => $request->input('cliente'),
                'forma_pago' => $request->input('forma_pago'),
                'cantidad_productos' => count($request->input('productos', []))
            ]);

            $data = $request->validate([
                'cliente'        => 'nullable|string|max:40',
                'cliente_nit'    => 'nullable|string|max:40',
                'forma_pago'     => 'sometimes|in:efectivo,transferencia,tarjeta',  // Bug #23: cambio a 'sometimes' para permitir default
                // Solo validar existencia; no validar montos
                'total_pagado'   => 'required',
                'productos'      => 'required|array|min:1',
                'productos.*.id' => 'required|exists:productos,id',
                'productos.*.cantidad' => 'required|integer|min:1',
                'productos.*.precio'   => 'required|numeric|min:0',
                'productos.*.iva'      => 'nullable|numeric|min:0|max:100',
            ]);

            // Nota: no se normaliza aquí para no imponer validación de montos.

            $caja = Caja::where('estado', 'abierta')->first();
            if (!$caja) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay caja abierta'
                ], 409);
            }

            // Bug #23: Forzar forma_pago a 'efectivo' si viene vacio o null
            $data['forma_pago'] = $data['forma_pago'] ?? 'efectivo';

            $empresa = Empresa::first();
            // Determina si actualmente la empresa cobra IVA (por defecto true si no existe registro)
            $cobraIva = $empresa ? (bool) $empresa->cobra_iva : true;

            DB::beginTransaction();

            // Validar stock
            foreach ($data['productos'] as $item) {
                $producto = Producto::find($item['id']);
                if (!$producto || !$producto->activo) {
                    Log::warning('Producto no disponible', [
                        'producto_id' => $item['id'],
                        'motivo' => !$producto ? 'No existe' : 'Inactivo'
                    ]);
                    throw new \Exception("El producto no está disponible");
                }
                // Bug #21: Validar que cantidad sea <= stock actual (doble validación contra oversell)
                if ($producto->stock < $item['cantidad']) {
                    Log::warning('Stock insuficiente', [
                        'producto_id' => $item['id'],
                        'producto_nombre' => $producto->nombre,
                        'stock_disponible' => $producto->stock,
                        'cantidad_solicitada' => $item['cantidad']
                    ]);
                    throw new \Exception("No hay suficiente stock de '{$producto->nombre}'. Disponible: {$producto->stock}");
                }
            }

            // Calcular totales y preparar detalles (un solo loop para evitar inconsistencias)
            $totalNeto = 0;
            $totalIva  = 0;
            $productosCalculados = [];

            foreach ($data['productos'] as $item) {
                $producto = Producto::findOrFail($item['id']);
                $precioBase = $producto->precio;
                $ivaRate = $cobraIva ? $producto->iva : 0;
                
                $neto = $item['cantidad'] * $precioBase;
                $iva  = round($neto * $ivaRate / 100, 2);
                $final = $neto + $iva;
                
                $totalNeto += $neto;
                $totalIva  += $iva;
                
                // Guardar cálculos para reutilizar sin recalcular
                $productosCalculados[] = [
                    'item' => $item,
                    'producto' => $producto,
                    'precioBase' => $precioBase,
                    'ivaRate' => $ivaRate,
                    'neto' => $neto,
                    'iva' => $iva,
                    'final' => $final
                ];
            }
            $totalFinal = $totalNeto + $totalIva;

            // Nota: Se permite guardar aunque `total_pagado` sea menor que `totalFinal`.

            // Venta
            $venta = Venta::create([
                'cliente' => $data['cliente'] ?? null,
                'forma_pago' => $data['forma_pago'] ?? null,
                'total'   => $totalFinal,
                'estado'  => 'completada',
                'fecha'   => now(),
                'user_id' => Auth::id(),
                'caja_id' => $caja->id,
            ]);

            // Factura
            $ultimoNumero = DB::table('facturas')
                ->whereYear('created_at', now()->year)
                ->max('numero');

            $nuevoNumero = $ultimoNumero
                ? ((int) substr($ultimoNumero, -6)) + 1
                : 1;
            $numeroFactura = 'FA-' . now()->year . '-' . str_pad($nuevoNumero, 6, '0', STR_PAD_LEFT);

            DB::table('facturas')->insert([
                'numero'         => $numeroFactura,
                'venta_id'       => $venta->id,
                'fecha_emision'  => now(),
                'cliente_nombre' => $data['cliente'] ?? null,
                'cliente_nit'    => $data['cliente_nit'] ?? null,
                'total'          => $totalFinal,
                'impuestos'      => $totalIva,
                'forma_pago'     => $data['forma_pago'] ?? null,
                'user_id'        => Auth::id(),
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);

            // Detalles y stock (usando cálculos previamente hechos, SIN recalcular IVA)
            foreach ($productosCalculados as $calc) {
                VentaDetalle::create([
                    'venta_id'        => $venta->id,
                    'producto_id'     => $calc['item']['id'],
                    'cantidad'        => $calc['item']['cantidad'],
                    'precio_unitario' => $calc['precioBase'],
                    'iva'             => $calc['iva'],
                    'subtotal'        => $calc['final'],
                    'total_pagado'    => $data['total_pagado'],
                ]);

                InventarioMovimiento::salida(
                    $calc['item']['id'],
                    $calc['item']['cantidad'],
                    'venta',
                    $venta->id,
                    "Venta #{$venta->id}",
                    Auth::id()
                );
            }

            DB::commit();

            Log::info('Venta registrada exitosamente', [
                'venta_id' => $venta->id,
                'cliente' => $data['cliente'] ?? 'Sin especificar',
                'forma_pago' => $data['forma_pago'],
                'total' => $totalFinal,
                'cantidad_productos' => count($productosCalculados),
                'timestamp' => now()
            ]);

            return response()->json([
                'success'   => true,
                'message'   => 'Venta registrada correctamente',
                'venta_id'  => $venta->id,
                'total'     => $totalFinal,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::warning('Error de validación en venta', [
                'errores' => $e->errors(),
                'timestamp' => now()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Por favor verifica los datos ingresados',
                'errors'  => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al registrar venta', [
                'mensaje' => $e->getMessage(),
                'linea' => $e->getLine(),
                'archivo' => $e->getFile(),
                'timestamp' => now()
            ]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    // Listado de ventas - Soluciona N+1 queries (#1) + Paginación en BD (#2)
    public function index(Request $request)
    {
        $registrosPorPagina = 10;
        $perPage = $request->input('per_page', $registrosPorPagina);
        if (!is_numeric($perPage)) {
            $perPage = $registrosPorPagina;
        }
        $perPage = (int) $perPage;
        if ($perPage < 1 || $perPage > 100) {
            $perPage = $registrosPorPagina;
        }

        $query = Venta::with('factura', 'detalles.producto')
            ->orderByDesc('fecha');

        // Filtro: texto (multi-columna)
        if ($search = trim($request->input('search', ''))) {
            $query->where(function ($q) use ($search) {
                $like = '%' . strtolower($search) . '%';

                // Buscar en columnas propias
                $q->whereRaw('LOWER(CAST(total AS CHAR)) LIKE ?', [$like])
                  ->orWhereRaw('LOWER(estado) LIKE ?', [$like]);

                // Buscar en relación factura (numero, cliente, documento)
                $q->orWhereHas('factura', function ($q2) use ($search, $like) {
                    $q2->whereRaw('LOWER(numero) LIKE ?', [$like])
                       ->orWhereRaw('LOWER(cliente_nombre) LIKE ?', [$like])
                       ->orWhereRaw('LOWER(cliente_nit) LIKE ?', [$like]);
                });
            });
        }

        // Filtro: estado (param name: status)
        if ($status = $request->input('status')) {
            $query->where('estado', $status);
        }

        // Filtro: rango de fechas sobre created_at
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        if ($dateFrom || $dateTo) {
            try {
                if ($dateFrom && $dateTo) {
                    $start = Carbon::parse($dateFrom)->startOfDay();
                    $end = Carbon::parse($dateTo)->endOfDay();
                    $query->whereBetween('created_at', [$start, $end]);
                } elseif ($dateFrom) {
                    $start = Carbon::parse($dateFrom)->startOfDay();
                    $query->where('created_at', '>=', $start);
                } elseif ($dateTo) {
                    $end = Carbon::parse($dateTo)->endOfDay();
                    $query->where('created_at', '<=', $end);
                }
            } catch (\Exception $e) {
                // Ignorar formato inválido de fecha para no romper la vista
                Log::warning('Fecha de filtro inválida en index ventas: ' . $e->getMessage());
            }
        }

        // Ejecutar paginación luego de aplicar filtros
        $ventas = $query->paginate($perPage)->appends($request->query());

        // Si la request es AJAX o espera JSON, devolver JSON con data y meta
        if ($request->ajax() || $request->wantsJson()) {
            // Transformar colección para enviar solo campos necesarios y evitar serializar toda la entidad
            $ventas->getCollection()->transform(function ($venta) {
                return [
                    'id' => $venta->id,
                    'numero_factura' => optional($venta->factura)->numero ?? null,
                    'fecha' => formatoHoraInteligente($venta->fecha) ?? null,
                    'cliente' => optional($venta->factura)->cliente_nombre ?? 'Consumidor final',
                    'total' => $venta->total ?? 0,
                    'impuestos' => optional($venta->factura)->impuestos ?? 0,
                    'forma_pago' => optional($venta->factura)->forma_pago ?? '-',
                    'estado' => $venta->estado ?? '---',
                    'puede_anular' => (
                        ($venta->estado === 'completada') &&
                        ($venta->factura && optional($venta->factura)->fecha_emision && Carbon::parse($venta->factura->fecha_emision)->isSameDay(Carbon::now()))
                    ),
                ];
            });

            return response()->json($ventas);
        }

        $empresa = Empresa::first();
        return view('ventas.index', compact('ventas', 'empresa', 'registrosPorPagina'));
    }

    // Mostrar factura (devuelve fragmento HTML para modal)
    public function show(Venta $venta)
    {
        $venta->load('detalles.producto', 'factura');
        $factura = $venta->factura;

        $empresa = Empresa::first();
        return view('ventas.show', compact('venta', 'factura', 'empresa'));
    }

    // Mostrar detalle de venta (nueva vista)
    public function detalle(Venta $venta)
    {
        $venta->load('detalles.producto', 'factura');
        $factura = $venta->factura;
        $empresa = Empresa::first();

        // Preparar totales simples para evitar lógica pesada en la vista
        $detalles = $venta->detalles;

        // Subtotal neto (precio_unitario * cantidad)
        $subtotal = $detalles->sum(function ($d) {
            return ($d->precio_unitario * $d->cantidad);
        });

        // IVA total (si está guardado por detalle)
        $totalIva = $detalles->sum('iva');

        // Total final (usar factura->total si existe, sino calcular)
        $total = $factura->total ?? $venta->total ?? ($subtotal + $totalIva);

        // Total pagado (si se guardó en los detalles, buscar el primer valor no nulo)
        $totalPagado = null;
        if ($detalles->isNotEmpty()) {
            $totalPagado = $detalles->first()->total_pagado ?? null;
        }

        // Calcular cambio o saldo (null si no hay info de pago)
        $cambio = null;
        if (!is_null($totalPagado)) {
            $cambio = $totalPagado - $total;
        }

        // Obtener nombre del vendedor si es posible (evitar consultas innecesarias)
        $vendedorNombre = '-';
        if ($venta->user_id) {
            $user = \App\Models\User::find($venta->user_id);
            if ($user) {
                $vendedorNombre = $user->name ?? ($user->nombre ?? $user->email ?? ('Usuario #' . $user->id));
            }
        }

        return view('ventas.detalle', compact(
            'venta',
            'factura',
            'empresa',
            'subtotal',
            'totalIva',
            'total',
            'totalPagado',
            'cambio',
            'vendedorNombre'
        ));
    }

    // Mostrar formulario de anulación (modal)
    public function devolucion(Venta $venta)
    {
        $empresa = Empresa::first();
        return view('ventas.devolucion', compact('venta', 'empresa'));
    }

    // Mostrar factura en página separada
    public function factura(Venta $venta)
    {
        $venta->load('detalles.producto', 'factura');
        $empresa = Empresa::first();

        return view('ventas.factura', compact('venta', 'empresa'));
    }

    // Confirmar anulación
    public function confirmarDevolucion(Request $request, Venta $venta)
    {
        $data = $request->validate([
            'motivo' => 'required|string|min:2|max:350',
        ], [
            'motivo.required' => 'El motivo de anulación es obligatorio.',
            'motivo.string'   => 'El motivo de anulación debe ser un texto.',
            'motivo.min'      => 'El motivo de anulación debe tener al menos 2 caracteres.',
            'motivo.max'      => 'El motivo de anulación no debe exceder los 350 caracteres.'
        ]);

        // Cargar relaciones
        $venta->load('detalles', 'factura');

        // Validaciones
        if ($venta->estado === 'anulada') {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'La venta ya está anulada'], 400);
            }
            return redirect()->back()->with('error', 'La venta ya está anulada');
        }

        if (!$venta->factura || !optional($venta->factura)->fecha_emision) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'No se encuentra la factura o fecha de emisión'], 400);
            }
            return redirect()->back()->with('error', 'No se encuentra la factura o fecha de emisión');
        }

        $fechaEmision = Carbon::parse(optional($venta->factura)->fecha_emision);
        if (!$fechaEmision->isSameDay(Carbon::now())) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Solo se pueden anular ventas emitidas hoy'], 400);
            }
            return redirect()->back()->with('error', 'Solo se pueden anular ventas emitidas hoy');
        }

        DB::beginTransaction();
        try {
            // Cambiar estado y (si existen) registrar motivo/fecha de anulación en la venta
            $venta->estado = 'anulada';
            if (Schema::hasColumn('ventas', 'motivo_anulacion')) {
                $venta->motivo_anulacion = $data['motivo'];
            }
            if (Schema::hasColumn('ventas', 'fecha_anulacion')) {
                $venta->fecha_anulacion = Carbon::now();
            }
            $venta->save();

            // Guardar motivo de anulación en cada detalle si la columna existe en ventas_detalle
            if (Schema::hasColumn('ventas_detalle', 'motivo_anulacion')) {
                foreach ($venta->detalles as $detalle) {
                    $detalle->motivo_anulacion = $data['motivo'];
                    $detalle->save();
                }
            }

            // Restaurar stock y registrar movimientos (tipo entrada, origen venta_anulada)
            foreach ($venta->detalles as $detalle) {
                $producto = Producto::find($detalle->producto_id);
                
                // Validación: el producto DEBE existir para registrar movimiento
                if (!$producto) {
                    throw new \Exception("El producto ID {$detalle->producto_id} no existe. No se puede anular la venta sin integridad de inventario.");
                }

                $stockAnterior = (int) $producto->stock;
                $cantidad = (int) $detalle->cantidad;
                $stockNuevo = $stockAnterior + $cantidad;

                $descripcion = 'Anulación venta: ' . $data['motivo'] . ". Stock anterior: {$stockAnterior}. Stock nuevo: {$stockNuevo}";

                InventarioMovimiento::entrada(
                    $detalle->producto_id,
                    $cantidad,
                    'venta_anulada',
                    $venta->id,
                    $descripcion,
                    Auth::id()
                );
            }

            DB::commit();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Venta anulada correctamente', 'venta_id' => $venta->id]);
            }
            return redirect()->route('ventas.index')->with('success', 'Venta anulada correctamente');

        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Error al anular la venta: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Error al anular la venta: ' . $e->getMessage());
        }
    }

    // Obtener todos los productos
    public function obtenerTodosProductos()
    {
        $productos = Producto::activos()
            ->select('id', 'nombre', 'precio', 'stock', 'iva')
            ->orderByDesc('stock')
            ->get();

        return response()->json($this->normalizarIVA($productos));
    }

    // Descargar factura en PDF
    public function descargarPDF(Venta $venta)
    {
        $venta->load('detalles.producto', 'factura');
        $empresa = Empresa::first();

        $pdf = \PDF::loadView('ventas.factura-pdf', [
            'venta' => $venta,
            'empresa' => $empresa
        ]);

        $nombreArchivo = 'factura-' . ($venta->factura->numero ?? $venta->id) . '.pdf';

        return $pdf->download($nombreArchivo);
    }

    // Vista para impresión (recibo thermal)
    public function impresion(Venta $venta)
    {
        $venta->load('detalles.producto', 'factura');
        $empresa = Empresa::first();

        return view('ventas.factura-impresion', [
            'venta' => $venta,
            'empresa' => $empresa
        ]);
    }

}