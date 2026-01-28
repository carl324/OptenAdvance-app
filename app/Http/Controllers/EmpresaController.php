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
        // Si viene un campo específico, validar solo ese campo (AJAX por-campo)
        if ($request->has('campo')) {
            $campo = $request->input('campo');
            $valor = $request->input('valor');

            // Normalizar valor vacío a null para reglas nullable
            if ($valor === '') {
                $valor = null;
            }

            // Mapas de reglas por campo
            $rulesMap = [
                'email' => 'nullable|email|max:255',
                'nit' => 'nullable|string|max:100',
                'nombre' => 'nullable|string|max:255',
                'direccion' => 'nullable|string|max:500',
                'telefono' => 'nullable|string|max:50',
                'moneda' => 'nullable|string|max:10',
                'cobra_iva' => 'nullable|in:0,1',
            ];

            $messagesMap = [
                'email.email' => 'El correo no es válido.',
                'email.max' => 'El correo es demasiado largo.',

                'nit.string' => 'El NIT debe ser texto.',
                'nit.max' => 'El NIT es demasiado largo (máximo 100 caracteres).',

                'nombre.string' => 'El nombre debe ser texto válido.',
                'nombre.max' => 'El nombre no debe exceder los 255 caracteres.',

                'direccion.string' => 'La dirección debe ser texto.',
                'direccion.max' => 'La dirección es demasiado larga.',

                'telefono.string' => 'El teléfono debe ser texto.',
                'telefono.max' => 'El teléfono es demasiado largo.',

                'moneda.string' => 'La moneda debe ser texto (ej: COP, USD).',
                'moneda.max' => 'La moneda no debe exceder 10 caracteres.',

                'cobra_iva.in' => 'Valor inválido para cobrar IVA.',
            ];

            if (!array_key_exists($campo, $rulesMap)) {
                return response()->json(['success' => false, 'message' => 'Campo no válido.'], 400);
            }

            $validator = \Validator::make([$campo => $valor], [$campo => $rulesMap[$campo]], $messagesMap);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            // Obtener o crear la empresa (primera fila)
            $empresa = Empresa::firstOrCreate([]);

            // Normalizar y guardar solo el campo recibido
            if ($campo === 'cobra_iva') {
                $empresa->cobra_iva = in_array($valor, [1, '1', true, 'true'], true) ? 1 : 0;
            } else {
                $empresa->{$campo} = $valor;
            }
            $empresa->save();

            return response()->json(['success' => true, 'message' => 'Campo guardado.']);
        }

        // Fallback: petición no por-campo -> validar todo y guardar
        $input = $request->all();

        $rules = [
            'nombre' => 'nullable|string|max:255',
            'nit' => 'nullable|string|max:100',
            'direccion' => 'nullable|string|max:500',
            'telefono' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'moneda' => 'nullable|string|max:10',
            'cobra_iva' => 'nullable|in:0,1',
        ];

        $messages = [
            'email.email' => 'El email debe tener un formato válido.',
            'email.max' => 'El email es demasiado largo.',
            'nit.max' => 'El NIT es demasiado largo (máximo 100 caracteres).',
            'nombre.max' => 'El nombre no debe exceder los 255 caracteres.',
            'moneda.max' => 'La moneda no debe exceder 10 caracteres.',
        ];

        $validator = \Validator::make($input, $rules, $messages);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $input['cobra_iva'] = $request->has('cobra_iva') ? 1 : 0;
        $existingId = Empresa::value('id');
        Empresa::updateOrCreate(['id' => $existingId], $input);
        return redirect()->route('empresa.index')->with('success', 'Los datos de la empresa se guardaron correctamente.');
    }
}
