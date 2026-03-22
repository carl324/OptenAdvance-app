<?php

namespace App\Http\Controllers;

use App\Models\Configuracion;
use App\Models\Devolucion;
use App\Models\DevolucionDetalle;
use App\Models\InventarioMovimiento;
use App\Models\MotivoDevolucion;
use App\Models\Producto;
use App\Models\Venta;
use App\Traits\Auditable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DevolucionController extends Controller
{
    use Auditable;

    public function create(Venta $venta)
    {
        $venta->load('detalles.producto', 'factura');
        $motivos = MotivoDevolucion::activos()->orderBy('nombre')->get();
        $diasDevolucion = (int) Configuracion::get('dias_devolucion', 3);

        $fechaEmision = Carbon::parse(optional($venta->factura)->fecha_emision);
        $diasTranscurridos = $fechaEmision->diffInDays(Carbon::now());

        if ($diasTranscurridos > $diasDevolucion) {
            return redirect()->route('ventas.detalle', $venta)
                ->with('error', "El plazo para realizar devoluciones es de {$diasDevolucion} días.");
        }

        $devuelto = DevolucionDetalle::whereHas('devolucion', fn($q) => $q->where('venta_id', $venta->id))
            ->select('venta_detalle_id', DB::raw('SUM(cantidad_devuelta) as total_devuelto'))
            ->groupBy('venta_detalle_id')
            ->pluck('total_devuelto', 'venta_detalle_id');

        return view('ventas.devolucion', compact('venta', 'motivos', 'devuelto', 'diasDevolucion'));
    }

    public function store(Request $request, Venta $venta)
    {
        $data = $request->validate([
            'motivo_devolucion_id'          => 'required|exists:motivos_devolucion,id',
            'observacion'                   => 'nullable|string|max:500',
            'metodo_reembolso'              => 'required|in:efectivo,transferencia,nota_credito',
            'monto_real'                    => 'required|numeric|min:0',
            'productos'                     => 'required|array|min:1',
            'productos.*.venta_detalle_id'  => 'required|exists:ventas_detalle,id',
            'productos.*.cantidad_devuelta' => 'required|numeric|min:0.01',
        ]);

        $venta->load('detalles.producto', 'factura');

        $diasDevolucion = (int) Configuracion::get('dias_devolucion', 3);
        $fechaEmision = Carbon::parse(optional($venta->factura)->fecha_emision);
        if ($fechaEmision->diffInDays(Carbon::now()) > $diasDevolucion) {
            return response()->json(['success' => false, 'message' => "El plazo de devolución es de {$diasDevolucion} días."], 400);
        }

        DB::beginTransaction();
        try {
            $montoCalculado = 0;
            $productosCalculados = [];

            foreach ($data['productos'] as $item) {
                $detalle = $venta->detalles->firstWhere('id', $item['venta_detalle_id']);

                if (!$detalle) {
                    throw new \Exception("Producto no pertenece a esta venta.");
                }

                $yaDevuelto = DevolucionDetalle::where('venta_detalle_id', $detalle->id)
                    ->sum('cantidad_devuelta');

                $disponible = $detalle->cantidad - $yaDevuelto;

                if ($item['cantidad_devuelta'] > $disponible) {
                    throw new \Exception("Solo puede devolver hasta {$disponible} unidades de {$detalle->producto->nombre}.");
                }

                $subtotal = (int) ($item['cantidad_devuelta'] * $detalle->precio_unitario);
                $montoCalculado += $subtotal;

                $productosCalculados[] = [
                    'venta_detalle_id' => $detalle->id,
                    'producto_id'      => $detalle->producto_id,
                    'cantidad'         => $item['cantidad_devuelta'],
                    'precio_unitario'  => $detalle->precio_unitario,
                    'subtotal'         => $subtotal,
                    'producto'         => $detalle->producto,
                ];
            }

            $devolucion = Devolucion::create([
                'venta_id'             => $venta->id,
                'user_id'              => Auth::id(),
                'motivo_devolucion_id' => $data['motivo_devolucion_id'],
                'observacion'          => $data['observacion'] ?? null,
                'metodo_reembolso'     => $data['metodo_reembolso'],
                'monto_calculado'      => $montoCalculado,
                'monto_real'           => $data['monto_real'],
                'fecha'                => now(),
            ]);

            foreach ($productosCalculados as $calc) {
                DevolucionDetalle::create([
                    'devolucion_id'     => $devolucion->id,
                    'venta_detalle_id'  => $calc['venta_detalle_id'],
                    'producto_id'       => $calc['producto_id'],
                    'cantidad_devuelta' => $calc['cantidad'],
                    'precio_unitario'   => $calc['precio_unitario'],
                    'subtotal'          => $calc['subtotal'],
                ]);

                InventarioMovimiento::entrada(
                    $calc['producto_id'],
                    $calc['cantidad'],
                    'venta_anulada',
                    $venta->id,
                    "Devolución #{$devolucion->id} — Venta #{$venta->id}",
                    Auth::id()
                );
            }

            self::registrar(
                'devolucion_venta',
                'venta',
                $venta->id,
                ['estado' => $venta->estado],
                ['devolucion_id' => $devolucion->id, 'monto_real' => $data['monto_real']],
                "Devolución #{$devolucion->id} registrada para venta #{$venta->id}. Monto: \${$data['monto_real']}"
            );

            DB::commit();
            $venta->recalcularSaldo((int) $montoCalculado);
            $venta->recalcularEstado();


            return response()->json([
                'success'         => true,
                'message'         => 'Devolución registrada correctamente',
                'devolucion_id'   => $devolucion->id,
                'monto_calculado' => $montoCalculado,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al registrar devolución', [
                'mensaje'  => $e->getMessage(),
                'venta_id' => $venta->id,
            ]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
}