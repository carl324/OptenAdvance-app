<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Traits\Auditable;

class ClienteController extends Controller
{
    use Auditable;
    public function index(Request $request)
    {
        $query = Cliente::orderBy('nombre');

        if ($search = trim($request->input('search', ''))) {
    $query->where(function ($q) use ($search) {
        $q->where('nombre', 'LIKE', '%' . $search . '%')
          ->orWhere('telefono', 'LIKE', '%' . $search . '%')
          ->orWhere('nit', 'LIKE', '%' . $search . '%');
    });
}

        $clientes = $query->paginate(15)->appends($request->query());

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($clientes);
        }

        return view('clientes.index', compact('clientes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'       => 'required|string|max:100',
            'telefono'     => 'nullable|string|max:30',
            'email'        => 'nullable|email|max:100',
            'nit'          => 'nullable|string|max:50',
            'direccion'    => 'nullable|string|max:255',
            'cupo_credito' => 'nullable|integer|min:0',
        ]);

        $cliente = Cliente::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Cliente creado correctamente',
            'cliente' => $cliente,
        ]);
    }

    public function show(Cliente $cliente)
{
    $cliente->load(['ventas' => function ($q) {
        $q->whereIn('estado', ['credito', 'parcial'])->orderByDesc('fecha');
    }, 'abonos']);

    $totalComprado = $cliente->ventas()->sum('total');
    $totalAbonado  = $cliente->abonos()->sum('monto');

    return view('clientes.show', compact('cliente', 'totalComprado', 'totalAbonado'));
}

    public function update(Request $request, Cliente $cliente)
    {
        $data = $request->validate([
            'nombre'       => 'required|string|max:100',
            'telefono'     => 'nullable|string|max:30',
            'email'        => 'nullable|email|max:100',
            'nit'          => 'nullable|string|max:50',
            'direccion'    => 'nullable|string|max:255',
            'cupo_credito' => 'nullable|integer|min:0',
        ]);

        $cliente->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Cliente actualizado correctamente',
            'cliente' => $cliente,
        ]);
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete(); // SoftDelete

        return response()->json([
            'success' => true,
            'message' => 'Cliente eliminado correctamente',
        ]);
    }

    // Buscar clientes para el selector del POS
    public function buscar(Request $request)
    {
        $q = $request->input('q', '');

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $clientes = Cliente::where('nombre', 'LIKE', '%' . $q . '%')
    ->orWhere('nit', 'LIKE', '%' . $q . '%')
    ->orWhere('telefono', 'LIKE', '%' . $q . '%')
            ->select('id', 'nombre', 'telefono', 'nit', 'saldo_pendiente', 'cupo_credito')
            ->limit(10)
            ->get();

        return response()->json($clientes);
    }

    // Registrar abono a una venta
    public function abonar(Request $request, Cliente $cliente)
    {
        $data = $request->validate([
            'venta_id'   => 'required|exists:ventas,id',
            'monto'      => 'required|integer|min:1',
            'forma_pago' => 'required|in:efectivo,transferencia,tarjeta',
            'observacion'=> 'nullable|string|max:255',
        ]);

        $venta = \App\Models\Venta::findOrFail($data['venta_id']);

        if ($venta->cliente_id !== $cliente->id) {
            return response()->json(['success' => false, 'message' => 'La venta no pertenece a este cliente'], 403);
        }

        if ($data['monto'] > $venta->saldo_pendiente) {
            return response()->json(['success' => false, 'message' => 'El abono supera el saldo pendiente'], 422);
        }

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            \App\Models\Abono::create([
                'venta_id'    => $venta->id,
                'cliente_id'  => $cliente->id,
                'user_id'     => \Illuminate\Support\Facades\Auth::id(),
                'monto'       => $data['monto'],
                'forma_pago'  => $data['forma_pago'],
                'observacion' => $data['observacion'] ?? null,
            ]);

            $nuevoSaldo = $venta->saldo_pendiente - $data['monto'];
            self::registrar(
    'abono_credito',
    'venta',
    $venta->id,
    ['saldo_pendiente' => $venta->saldo_pendiente, 'estado' => $venta->estado],
    ['saldo_pendiente' => $nuevoSaldo, 'estado' => $nuevoSaldo <= 0 ? 'completada' : 'parcial'],
    "Abono de \${$data['monto']} a venta #{$venta->id} del cliente {$cliente->nombre}. Forma de pago: {$data['forma_pago']}"
);
            $venta->saldo_pendiente = $nuevoSaldo;
            $venta->estado = $nuevoSaldo <= 0 ? 'completada' : 'parcial';
            $venta->save();

            $cliente->decrement('saldo_pendiente', $data['monto']);

            \Illuminate\Support\Facades\DB::commit();

            return response()->json([
                'success'         => true,
                'message'         => 'Abono registrado correctamente',
                'saldo_pendiente' => $nuevoSaldo,
                'estado_venta'    => $venta->estado,
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}