<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Traits\Auditable;

class CajaController extends Controller
{
    use Auditable;

    public function resumenCierre()
    {
        $caja = Caja::where('estado', 'abierta')->first();

        if (!$caja) {
            return response()->json([
                'success' => false,
                'message' => 'No hay caja abierta'
            ], 409);
        }

        // Considerar solo ventas válidas (no anuladas/canceladas) para totales
       

        $stats = Venta::where('caja_id', $caja->id)
    ->selectRaw("
        COUNT(CASE WHEN estado NOT IN ('anulada','cancelada') THEN 1 END) as cantidad_ventas,
        COALESCE(SUM(CASE WHEN estado NOT IN ('anulada','cancelada') THEN total ELSE 0 END), 0) as total_ingresos,
        COALESCE(SUM(CASE WHEN forma_pago='efectivo' AND estado NOT IN ('anulada','cancelada') THEN total ELSE 0 END), 0) as total_efectivo,
        COALESCE(SUM(CASE WHEN forma_pago='tarjeta' AND estado NOT IN ('anulada','cancelada') THEN total ELSE 0 END), 0) as total_tarjeta,
        COALESCE(SUM(CASE WHEN forma_pago='transferencia' AND estado NOT IN ('anulada','cancelada') THEN total ELSE 0 END), 0) as total_transferencia,
        COALESCE(SUM(CASE WHEN forma_pago='efectivo' AND estado='anulada' THEN total ELSE 0 END), 0) as devoluciones_efectivo
    ")
    ->first();

$cantidadVentas      = (int) $stats->cantidad_ventas;
$totalIngresos       = (float) $stats->total_ingresos;
$totalEfectivo       = (float) $stats->total_efectivo;
$totalTarjeta        = (float) $stats->total_tarjeta;
$totalTransferencia  = (float) $stats->total_transferencia;
$devolucionesEfectivo = (float) $stats->devoluciones_efectivo;
$totalOtros          = $totalIngresos - ($totalEfectivo + $totalTarjeta + $totalTransferencia);
$montoCierreCalculado = (float) $caja->monto_apertura + $totalEfectivo;



        return response()->json([
            'success' => true,
            'caja_id' => $caja->id,
            'total_ventas_cantidad' => $cantidadVentas,
            'total_ingresos' => $totalIngresos,
            'total_efectivo' => $totalEfectivo,
            'total_tarjeta' => $totalTarjeta,
            'total_transferencia' => $totalTransferencia,
            'total_otros' => $totalOtros,
            'monto_apertura' => (float) $caja->monto_apertura,
            'monto_cierre_calculado' => $montoCierreCalculado,
            'devoluciones_efectivo' => $devolucionesEfectivo,
        ]);
    }

    public function abrir(Request $request)
    {
        if (Caja::where('estado', 'abierta')->exists()) {
            return response()->json(['success' => false, 'message' => 'Ya existe una caja abierta'], 409);
        }

        $data = $request->validate([
            'monto_apertura' => ['required', 'numeric', 'min:0'],
            'nota_apertura'  => ['nullable', 'string', 'max:255'],
        ]);

        $caja = Caja::create([
            'user_id'        => Auth::id(),
            'fecha_apertura' => now(),
            'monto_apertura' => $data['monto_apertura'],
            'nota_apertura'  => $data['nota_apertura'] ?? null,
            'estado'         => 'abierta',
        ]);

        // ── Auditoría ──
        self::registrar(
            'apertura_caja',
            'caja',
            $caja->id,
            null,
            ['monto_apertura' => $data['monto_apertura'], 'nota' => $data['nota_apertura'] ?? null],
            "Apertura de caja #{$caja->id} con fondo \${$data['monto_apertura']}"
        );

        Cache::forget('app_caja_actual_data');

        if (!$request->wantsJson()) {
            return redirect()->route('ventas.create');
        }

        return response()->json(['success' => true, 'caja_id' => $caja->id]);
    }

    public function cerrar(Request $request)
    {
        $caja = Caja::where('estado', 'abierta')->first();

        if (!$caja) {
            return response()->json(['success' => false, 'message' => 'No hay caja abierta'], 409);
        }

        $data = $request->validate([
            'monto_cierre_real' => ['required', 'numeric', 'min:0'],
            'nota_cierre'       => ['nullable', 'string', 'max:255'],
            'imprimir'          => ['nullable', 'boolean'],
        ]);



        $stats = Venta::where('caja_id', $caja->id)
    ->selectRaw("
        COALESCE(SUM(CASE WHEN estado NOT IN ('anulada','cancelada') THEN total ELSE 0 END), 0) as total_ventas,
        COALESCE(SUM(CASE WHEN forma_pago='efectivo' AND estado NOT IN ('anulada','cancelada') THEN total ELSE 0 END), 0) as total_efectivo
    ")
    ->first();

$totalVentas   = (float) $stats->total_ventas;
$totalEfectivo = (float) $stats->total_efectivo;

        $montoCierreCalculado = (float) $caja->monto_apertura + $totalEfectivo;
        $diferencia           = (float) $data['monto_cierre_real'] - $montoCierreCalculado;

        // Snapshot antes
        $antes = [
            'estado'         => 'abierta',
            'monto_apertura' => (float) $caja->monto_apertura,
            'total_ventas'   => $totalVentas,
        ];

        $caja->update([
            'fecha_cierre'            => now(),
            'total_ventas'            => $totalVentas,
            'total_efectivo'          => $totalEfectivo,
            'monto_cierre_calculado'  => $montoCierreCalculado,
            'monto_cierre_real'       => $data['monto_cierre_real'],
            'diferencia'              => $diferencia,
            'nota_cierre'             => $data['nota_cierre'] ?? null,
            'estado'                  => 'cerrada',
            'user_cierre_id'          => Auth::id(),
        ]);

        // ── Auditoría ──
        self::registrar(
            'cierre_caja',
            'caja',
            $caja->id,
            $antes,
            [
                'estado'                 => 'cerrada',
                'monto_cierre_real'      => $data['monto_cierre_real'],
                'monto_cierre_calculado' => $montoCierreCalculado,
                'diferencia'             => $diferencia,
                'nota'                   => $data['nota_cierre'] ?? null,
            ],
            "Cierre de caja #{$caja->id}. Diferencia: \${$diferencia}"
        );

        Cache::forget('app_caja_actual_data');

        $printUrl = !empty($data['imprimir']) ? route('caja.cierre.print', $caja->id) : null;

        return response()->json(['success' => true, 'caja_id' => $caja->id, 'print_url' => $printUrl]);
    }

    public function printCierre(Caja $caja)
    {
        if ($caja->estado !== 'cerrada') {
            return redirect()->route('ventas.create');
        }

        // Cargar relaciones de usuarios
        $caja->load(['usuarioApertura', 'usuarioCierre']);

        return view('caja.cierre-print', compact('caja'));
    }
}