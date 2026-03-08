<?php

namespace App\Http\Controllers;

use App\Models\Configuracion;
use App\Models\MotivoDevolucion;
use Illuminate\Http\Request;

class ConfiguracionDevolucionController extends Controller
{
 public function devoluciones()
    {
        $diasDevolucion = Configuracion::get('dias_devolucion', 3);
        $motivos = MotivoDevolucion::orderBy('nombre')->get();
        return view('ajustes.devoluciones', compact('diasDevolucion', 'motivos'));
    }

    public function guardarDias(Request $request)
    {
        $request->validate([
            'dias_devolucion' => 'required|integer|min:1|max:365',
        ]);

        Configuracion::set('dias_devolucion', $request->dias_devolucion);

        return response()->json(['success' => true, 'message' => 'Configuración guardada']);
    }

    public function storeMotivoDevolucion(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100|unique:motivos_devolucion,nombre',
        ]);

        $motivo = MotivoDevolucion::create(['nombre' => $request->nombre, 'activo' => true]);

        return response()->json(['success' => true, 'motivo' => $motivo]);
    }

    public function updateMotivoDevolucion(Request $request, MotivoDevolucion $motivo)
    {
        $request->validate([
            'nombre' => 'required|string|max:100|unique:motivos_devolucion,nombre,' . $motivo->id,
        ]);

        $motivo->update(['nombre' => $request->nombre]);

        return response()->json(['success' => true, 'motivo' => $motivo]);
    }

    public function toggleMotivoDevolucion(MotivoDevolucion $motivo)
    {
        $motivo->update(['activo' => !$motivo->activo]);
        return response()->json(['success' => true, 'activo' => $motivo->activo]);
    }

    public function destroyMotivoDevolucion(MotivoDevolucion $motivo)
    {
        $motivo->delete();
        return response()->json(['success' => true]);
    }
}