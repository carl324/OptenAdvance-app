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
        // Permitir actualización parcial vía AJAX para cualquier campo
        if ($request->wantsJson()) {
            $empresa = Empresa::first();
            if (!$empresa) {
                return response()->json(['success' => false, 'message' => 'No existe empresa para actualizar.'], 404);
            }
            $campos = [
                'nombre' => 'string|max:255',
                'nit' => 'string|max:100',
                'direccion' => 'nullable|string|max:500',
                'telefono' => 'nullable|string|max:50',
                'email' => 'nullable|email|max:255',
                'moneda' => 'string|max:10',
                'cobra_iva' => 'nullable|boolean',
            ];
            $data = [];
            foreach ($campos as $campo => $reglas) {
                if ($request->has($campo)) {
                    $data[$campo] = $request->input($campo);
                }
            }
            // Validar solo los campos presentes
            $validator = \Validator::make($data, array_intersect_key($campos, $data));
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error de validación',
                    'errors' => $validator->errors()
                ], 422);
            }
            // Normalizar checkbox
            if (array_key_exists('cobra_iva', $data)) {
                $data['cobra_iva'] = $request->input('cobra_iva') ? 1 : 0;
            }
            $empresa->fill($data);
            $empresa->save();
            return response()->json(['success' => true, 'message' => 'Cambios guardados.']);
        }

        // Petición normal (no AJAX): validar todo
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'nit' => 'required|string|max:100',
            'direccion' => 'nullable|string|max:500',
            'telefono' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'moneda' => 'required|string|max:10',
            'cobra_iva' => 'nullable|boolean',
        ]);
        $validated['cobra_iva'] = $request->has('cobra_iva') ? 1 : 0;
        $existingId = Empresa::value('id');
        Empresa::updateOrCreate(
            ['id' => $existingId],
            $validated
        );
        return redirect()->route('empresa.index')->with('success', 'Los datos de la empresa se guardaron correctamente.');
    }
}
