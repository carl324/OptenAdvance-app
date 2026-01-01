<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route as RouteFacade;
use App\Models\Empresa;

class EmpresaController extends Controller
{
    /** Mostrar formulario de edición de la empresa (único registro) */
    public function edit()
    {
        // Obtener el primer (y único) registro de la tabla `empresa` (puede ser null)
        $empresa = Empresa::first();

        // Mostrar siempre la vista `empresa.index`. Si no existe empresa,
        // la vista debe mostrar el formulario vacío para registrar los datos.
        return view('empresa.index', compact('empresa'));
    }

    /** Actualizar datos de la empresa existente */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'nit' => 'required|string|max:100',
            'direccion' => 'nullable|string|max:500',
            'telefono' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'moneda' => 'required|string|max:10',
        ]);

        // Usar updateOrCreate para crear o actualizar el único registro de empresa.
        $existingId = Empresa::value('id');
        Empresa::updateOrCreate(
            ['id' => $existingId],
            $validated
        );

        // Volver a la página de edición con mensaje de éxito
        return redirect()->route('empresa.index')->with('success', 'Los datos de la empresa se guardaron correctamente.');
    }
}
