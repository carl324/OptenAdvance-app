<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\Producto;
use App\Models\InventarioMovimiento;
use App\Models\Empresa;

class VentaController extends Controller
{
    // Vista del formulario
    public function create()
    {
        return view('ventas.create');
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

        return response()->json($productos);
    }

    // Registrar venta
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'cliente'        => 'nullable|string|max:100',
                'cliente_nit'    => 'nullable|string|max:20',
                'forma_pago'     => 'nullable|string|max:50',
                'productos'      => 'required|array|min:1',
                'productos.*.id' => 'required|exists:productos,id',
                'productos.*.cantidad' => 'required|integer|min:1',
                'productos.*.precio'   => 'required|numeric|min:0',
                'productos.*.iva'      => 'nullable|numeric|min:0|max:100',
            ]);

            DB::beginTransaction();

            // Validar stock
            foreach ($data['productos'] as $item) {
                $producto = Producto::find($item['id']);
                if (!$producto || !$producto->activo) {
                    throw new \Exception("El producto no está disponible");
                }
                if ($producto->stock < $item['cantidad']) {
                    throw new \Exception("No hay suficiente stock de '{$producto->nombre}'. Disponible: {$producto->stock}");
                }
            }

            // Totales
            $totalNeto = 0;
            $totalIva  = 0;
            foreach ($data['productos'] as $item) {
                $neto = $item['cantidad'] * $item['precio'];
                $iva  = $neto * ($item['iva'] ?? 0) / 100;
                $totalNeto += $neto;
                $totalIva  += $iva;
            }
            $totalFinal = $totalNeto + $totalIva;

            // Venta
            $venta = Venta::create([
                'cliente' => $data['cliente'] ?? null,
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

            // Detalles y stock
            foreach ($data['productos'] as $item) {
                $neto  = $item['cantidad'] * $item['precio'];
                $iva   = $neto * ($item['iva'] ?? 0) / 100;
                $final = $neto + $iva;

                VentaDetalle::create([
                    'venta_id'        => $venta->id,
                    'producto_id'     => $item['id'],
                    'cantidad'        => $item['cantidad'],
                    'precio_unitario' => $item['precio'],
                    'iva'             => $iva,
                    'subtotal'        => $final,
                ]);

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
                'success'   => true,
                'message'   => 'Venta registrada correctamente',
                'venta_id'  => $venta->id,
                'total'     => $totalFinal,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Por favor verifica los datos ingresados',
                'errors'  => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    // Listado de ventas
    public function index()
    {
        $ventas = Venta::with('factura')
            ->orderByDesc('fecha')
            ->get();

        return view('ventas.index', compact('ventas'));
    }

    // Mostrar factura (devuelve fragmento HTML para modal)
    public function show(Venta $venta)
    {
        $venta->load('detalles.producto', 'factura');
        $factura = $venta->factura;

        return view('ventas.show', compact('venta', 'factura'));
    }

    // Mostrar formulario de anulación (modal)
    public function devolucion(Venta $venta)
    {
        return view('ventas.devolucion', compact('venta'));
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
            'motivo' => 'required|string|min:3|max:255',
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

        if (!$venta->factura || !$venta->factura->fecha_emision) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'No se encuentra la factura o fecha de emisión'], 400);
            }
            return redirect()->back()->with('error', 'No se encuentra la factura o fecha de emisión');
        }

        $fechaEmision = Carbon::parse($venta->factura->fecha_emision);
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

            // Restaurar stock y registrar movimientos (tipo entrada, origen venta_anulada)
            foreach ($venta->detalles as $detalle) {
                $producto = Producto::find($detalle->producto_id);
                $stockAnterior = $producto ? (int) $producto->stock : 0;
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

}
