<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Traits\Auditable;
use App\Models\Abono;

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
    $html = view('clientes._table', compact('clientes'))->render();

    return response()->json([
        'success'    => true,
        'html'       => $html,
        'pagination' => [
            'current_page' => $clientes->currentPage(),
            'last_page'    => $clientes->lastPage(),
        ],
    ]);
}

        return view('clientes.index', compact('clientes'));
    }

    public function store(Request $request)
    {
$data = $request->validate([
    'nombre'       => 'required|string|max:100',
    'telefono'     => 'nullable|string|max:30|unique:clientes,telefono',
    'email'        => 'nullable|email|max:100',
    'nit'          => 'nullable|string|max:50|unique:clientes,nit',
    'direccion'    => 'nullable|string|max:255',
    'cupo_credito' => 'nullable|integer|min:-1',
], [
    'telefono.unique' => 'Este teléfono ya está registrado.',
    'nit.unique'      => 'Este NIT ya está en uso.',
    'nombre.required' => 'El nombre es obligatorio.',
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

    // Cargar abonos paginados correctamente
    $abonos = $cliente->abonos()->orderByDesc('created_at')->paginate(5);

    return view('clientes.show', compact('cliente', 'totalComprado', 'totalAbonado', 'abonos'));
}
    public function update(Request $request, Cliente $cliente)
    {
$data = $request->validate([
    'nombre'       => 'required|string|max:100',
    'telefono'     => 'nullable|string|max:30|unique:clientes,telefono,' . $cliente->id,
    'email'        => 'nullable|email|max:100',
    'nit'          => 'nullable|string|max:50|unique:clientes,nit,' . $cliente->id,
    'direccion'    => 'nullable|string|max:255',
    'cupo_credito' => 'nullable|integer|min:-1',
], [
    'telefono.unique' => 'Este teléfono ya está registrado.',
    'nit.unique'      => 'Este NIT ya está en uso.',
    'nombre.required' => 'El nombre es obligatorio.',
]);

        $cliente->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Cliente actualizado correctamente',
            'cliente' => $cliente,
        ]);
    }
public function printAbonos(Cliente $cliente)
{
    $abonos  = $cliente->abonos()->orderByDesc('created_at')->get();
    $empresa = \App\Models\Empresa::first();
    return view('clientes.historial-abonos-print', compact('cliente', 'abonos', 'empresa'));
}
    public function listarAbonos(Request $request, Cliente $cliente)
{
    $query = $cliente->abonos();

    // Búsqueda por observación o forma de pago
    if ($search = trim($request->input('search', ''))) {
        $query->where(function ($q) use ($search) {
            $q->where('observacion', 'LIKE', '%' . $search . '%')
              ->orWhere('forma_pago', 'LIKE', '%' . $search . '%');
        });
    }

    // Filtro rango de fechas
    if ($fechaDesde = $request->input('fecha_desde')) {
        $query->where('created_at', '>=', $fechaDesde . ' 00:00:00');
    }
    if ($fechaHasta = $request->input('fecha_hasta')) {
        $query->where('created_at', '<=', $fechaHasta . ' 23:59:59');
    }

    $abonos = $query->orderByDesc('created_at')->paginate(5)->appends($request->query());

    if ($request->ajax() || $request->wantsJson()) {
        $html = view('clientes._table-abonos', compact('abonos'))->render();
        $pagination = [ 'current_page' => $abonos->currentPage(), 'last_page'    => $abonos->lastPage(),];

        return response()->json([
            'success'    => true,
            'html'       => $html,
            'pagination' => $pagination,
        ]);
    }
}

public function destroy(Cliente $cliente)
{
    if ($cliente->saldo_pendiente > 0) {
        return response()->json([
            'success' => false,
            'message' => 'Este cliente tiene una deuda pendiente de $' . number_format($cliente->saldo_pendiente, 0, ',', '.') . ' Salda la deuda antes de eliminarlo.',
        ], 422);
    }

    $cliente->delete();

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
            $abono = \App\Models\Abono::create([
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

            $cliente->refresh();

return response()->json([
    'success'             => true,
    'message'             => 'Abono registrado correctamente',
    'saldo_pendiente'     => $nuevoSaldo,
    'saldo_total_cliente' => $cliente->saldo_pendiente,
    'estado_venta'        => $venta->estado,
    'comprobante'         => [
        'abono_id'       => $abono->id,
        'cliente_nombre' => $cliente->nombre,
        'cliente_nombre' => $cliente->nombre,
        'cliente_nit'    => $cliente->nit,
        'venta_id'       => $venta->id,
        'monto'          => $data['monto'],
        'forma_pago'     => $data['forma_pago'],
        'observacion'    => $data['observacion'] ?? null,
        'saldo_venta'    => $nuevoSaldo,
        'saldo_cliente'  => $cliente->saldo_pendiente,
        'fecha'          => now()->format('d/m/Y H:i'),
    ],
]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

public function comprobante(Cliente $cliente, Abono $abono)
{
    if ($abono->cliente_id !== $cliente->id) {
        abort(403);
    }

    $venta   = \App\Models\Venta::findOrFail($abono->venta_id);
    $empresa = \App\Models\Empresa::first();

    return view('clientes.comprobante-abono', compact('cliente', 'abono', 'venta', 'empresa'));
}
}