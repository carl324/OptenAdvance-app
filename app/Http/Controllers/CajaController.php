<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CajaController extends Controller
{
    public function resumenCierre()
    {
        $caja = Caja::where('estado', 'abierta')->first();

        if (!$caja) {
            return response()->json([
                'success' => false,
                'message' => 'No hay caja abierta'
            ], 409);
        }

        $cantidadVentas = Venta::where('caja_id', $caja->id)->count();
        $totalIngresos = (float) Venta::where('caja_id', $caja->id)->sum('total');

        $totalEfectivo = (float) Venta::where('caja_id', $caja->id)
            ->where('forma_pago', 'efectivo')
            ->sum('total');

        $totalTarjeta = (float) Venta::where('caja_id', $caja->id)
            ->where('forma_pago', 'tarjeta')
            ->sum('total');

        $totalTransferencia = (float) Venta::where('caja_id', $caja->id)
            ->where('forma_pago', 'transferencia')
            ->sum('total');

        $totalOtros = $totalIngresos - ($totalEfectivo + $totalTarjeta + $totalTransferencia);

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
        ]);
    }

    public function abrir(Request $request)
    {
        if (Caja::where('estado', 'abierta')->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Ya existe una caja abierta'
            ], 409);
        }

        $data = $request->validate([
            'monto_apertura' => ['required', 'numeric', 'min:0'],
            'nota_apertura' => ['nullable', 'string', 'max:255'],
        ]);

        $caja = Caja::create([
            'user_id' => Auth::id(),
            'fecha_apertura' => now(),
            'monto_apertura' => $data['monto_apertura'],
            'nota_apertura' => $data['nota_apertura'] ?? null,
            'estado' => 'abierta',
        ]);

        if (!$request->wantsJson()) {
            return redirect()->route('ventas.create');
        }

        return response()->json([
            'success' => true,
            'caja_id' => $caja->id,
        ]);
    }

    public function cerrar(Request $request)
    {
        $caja = Caja::where('estado', 'abierta')->first();

        if (!$caja) {
            return response()->json([
                'success' => false,
                'message' => 'No hay caja abierta'
            ], 409);
        }

        $data = $request->validate([
            'monto_cierre_real' => ['required', 'numeric', 'min:0'],
            'nota_cierre' => ['nullable', 'string', 'max:255'],
            'imprimir' => ['nullable', 'boolean'],
        ]);

        $totalVentas = (float) Venta::where('caja_id', $caja->id)->sum('total');
        $totalEfectivo = (float) Venta::where('caja_id', $caja->id)
            ->where('forma_pago', 'efectivo')
            ->sum('total');

        $montoCierreCalculado = (float) $caja->monto_apertura + $totalEfectivo;
        $diferencia = (float) $data['monto_cierre_real'] - $montoCierreCalculado;

        $caja->update([
            'fecha_cierre' => now(),
            'total_ventas' => $totalVentas,
            'total_efectivo' => $totalEfectivo,
            'monto_cierre_calculado' => $montoCierreCalculado,
            'monto_cierre_real' => $data['monto_cierre_real'],
            'diferencia' => $diferencia,
            'nota_cierre' => $data['nota_cierre'] ?? null,
            'estado' => 'cerrada',
            'user_id' => Auth::id(),
        ]);

        $printUrl = null;
        if (!empty($data['imprimir'])) {
            $printUrl = route('caja.cierre.print', $caja->id);
        }

        return response()->json([
            'success' => true,
            'caja_id' => $caja->id,
            'print_url' => $printUrl,
        ]);
    }

    public function printCierre(Caja $caja)
    {
        if ($caja->estado !== 'cerrada') {
            return redirect()->route('ventas.create');
        }

        return view('caja.cierre-print', compact('caja'));
    }
}
