<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\Producto;
use App\Models\InventarioMovimiento;
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
        return view('ventas.create', compact('empresa'));
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
                'cliente'        => 'nullable|string|max:100',
                'cliente_nit'    => 'nullable|string|max:20',
                'forma_pago'     => 'sometimes|in:efectivo,transferencia,tarjeta',  // Bug #23: cambio a 'sometimes' para permitir default
                'productos'      => 'required|array|min:1',
                'productos.*.id' => 'required|exists:productos,id',
                'productos.*.cantidad' => 'required|integer|min:1',
                'productos.*.precio'   => 'required|numeric|min:0',
                'productos.*.iva'      => 'nullable|numeric|min:0|max:100',
            ]);

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

            // Venta
            $venta = Venta::create([
                'cliente' => $data['cliente'] ?? null,
                'forma_pago' => $data['forma_pago'] ?? null,
                'total'   => $totalFinal,
                'estado'  => 'completada',
                'fecha'   => now(),
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
                ]);

                InventarioMovimiento::salida(
                    $calc['item']['id'],
                    $calc['item']['cantidad'],
                    'venta',
                    $venta->id,
                    "Venta #{$venta->id}"
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
    public function index()
    {
        $registrosPorPagina = 10;
        $ventas = Venta::with('factura', 'detalles.producto')
            ->orderByDesc('fecha')
            ->paginate($registrosPorPagina);

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
                    $descripcion
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
