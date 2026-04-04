<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AuditoriaController extends Controller
{
    public function index(Request $request)
    {
        $perPage = 25;

        $query = DB::table('auditoria')
            ->leftJoin('users', 'users.id', '=', 'auditoria.user_id')
            ->select(
                'auditoria.id',
                'auditoria.tipo_accion',
                'auditoria.entidad',
                'auditoria.entidad_id',
                'auditoria.antes',
                'auditoria.despues',
                'auditoria.descripcion',
                'auditoria.ip',
                'auditoria.created_at',
                'users.name as usuario_nombre',
                'users.email as usuario_email',
            )
            ->orderByDesc('auditoria.created_at');

        // Filtro: fecha desde
        if ($desde = $request->input('desde')) {
            try {
                $query->where('auditoria.created_at', '>=', Carbon::parse($desde)->startOfDay());
            } catch (\Exception $e) {}
        }

        // Filtro: fecha hasta
        if ($hasta = $request->input('hasta')) {
            try {
                $query->where('auditoria.created_at', '<=', Carbon::parse($hasta)->endOfDay());
            } catch (\Exception $e) {}
        }

        // Filtro: usuario
        if ($userId = $request->input('user_id')) {
            $query->where('auditoria.user_id', $userId);
        }

        // Filtro: tipo de acción
        if ($tipo = $request->input('tipo_accion')) {
            $query->where('auditoria.tipo_accion', $tipo);
        }

        $registros = $query->paginate($perPage)->appends($request->query());

        // Decodificar JSON para la vista
        $registros->getCollection()->transform(function ($r) {
            $r->antes   = $r->antes   ? json_decode($r->antes, true)   : null;
            $r->despues = $r->despues ? json_decode($r->despues, true) : null;
            return $r;
        });

        $usuarios = DB::table('users')->select('id', 'name')->orderBy('name')->get();

        $tiposAccion = [
            'abono_credito'         => 'Abono a crédito',
            'anulacion_venta'       => 'Anulación de venta',
            'apertura_caja'         => 'Apertura de caja',
            'cierre_caja'           => 'Cierre de caja',
            'ajuste_inventario'     => 'Ajuste de inventario',
            'cambio_precio_producto'=> 'Cambio de precio',
            'cambio_nombre_producto'=> 'Cambio de nombre',
            'eliminacion_producto'  => 'Eliminación de producto',
        ];

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($registros);
        }

        return view('auditoria.index', compact('registros', 'usuarios', 'tiposAccion'));
    }
}