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
        $empresa = Empresa::first();

        if (! $empresa) {
            // Si existe una ruta de creación inicial, redirigimos ahí; si no, a la raíz
            if (RouteFacade::has('empresa.create')) {
                return redirect()->route('empresa.create');
            }

            return redirect('/');
        }

        return view('empresa.edit', compact('empresa'));
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

        $empresa = Empresa::first();
        if (! $empresa) {
            if (RouteFacade::has('empresa.create')) {
                return redirect()->route('empresa.create')->with('error', 'Empresa no encontrada, por favor cree la empresa inicial.');
            }
            return redirect('/')->with('error', 'Empresa no encontrada');
        }

        $empresa->update($validated);

        return redirect()->route('empresa.edit')->with('success', 'Datos de la empresa actualizados correctamente.');
    }
}
