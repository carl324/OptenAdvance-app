<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Empleado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class PersonalController extends Controller
{
    public function index()
    {
        // Usar el modelo Empleado que filtra automáticamente por activo = 1
        $query = Empleado::where('role', 'empleado');

        if (Auth::check() && Auth::user()->role === 'admin') {
            $query->where('id', '!=', Auth::id());
        }

        $empleados = $query->orderBy('id', 'desc')->get();
        $empleadosCount = $empleados->count();

        return view('personal.index', compact('empleados', 'empleadosCount'));
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->where(function ($query) {
                    return $query->where('activo', 1);
                }),
            ],
            'phone' => 'nullable|string|max:50',
            'password' => 'required|string|min:4',
        ];

        $messages = [
            'required' => 'Este campo es obligatorio.',
            'email' => 'El email no es válido.',
            'password.min' => 'La contraseña es muy corta. Mínimo :min caracteres.',
            'max' => 'Máximo :max caracteres permitidos.',
            'unique' => 'Este correo ya está en uso por otro usuario.'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();

        $empleado = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'role' => 'empleado',
            'activo' => 1,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Listo, el empleado quedó creado.',
                'empleado' => [
                    'id' => $empleado->id,
                    'name' => $empleado->name,
                    'email' => $empleado->email,
                    'phone' => $empleado->phone,
                ],
            ]);
        }

        return redirect()->route('personal.index');
    }

    public function update(Request $request, $id)
    {
        $empleado = Empleado::withoutGlobalScope('activo')->find($id);
        if (! $empleado) {
            return response()->json(['success' => false, 'message' => 'Empleado no encontrado.'], 404);
        }
        $rules = [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($empleado->id)->where(function ($query) {
                    return $query->where('activo', 1);
                }),
            ],
            'phone' => 'nullable|string|max:50',
            'password' => 'nullable|string|min:4',
        ];

        $messages = [
            'required' => 'Este campo es obligatorio.',
            'email' => 'El email no es válido.',
            'password.min' => 'La contraseña es muy corta. Mínimo :min caracteres.',
            'max' => 'Máximo :max caracteres permitidos.',
            'unique' => 'Este correo ya está en uso por otro usuario.'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }

            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();

        // Evitar cambios peligrosos (no permitir cambiar role aquí)
        $empleado->name = $data['name'];
        $empleado->email = $data['email'];
        $empleado->phone = $data['phone'] ?? null;
        if (!empty($data['password'])) {
            $empleado->password = Hash::make($data['password']);
        }
        $empleado->save();

        return response()->json([
            'success' => true,
            'message' => 'Empleado actualizado correctamente.',
            'user' => [
                'id' => $empleado->id,
                'name' => $empleado->name,
                'email' => $empleado->email,
                'phone' => $empleado->phone,
            ],
        ]);
    }

    public function destroy($id)
    {
        $empleado = Empleado::withoutGlobalScope('activo')->find($id);
        if (! $empleado) {
            return response()->json(['success' => false, 'message' => 'Empleado no encontrado.'], 404);
        }

        if (Auth::check() && Auth::id() == $empleado->id) {
            return response()->json(['success' => false, 'message' => 'No puede eliminar su propio usuario.'], 403);
        }

        if ($empleado->role === 'admin') {
            return response()->json(['success' => false, 'message' => 'No puede eliminar un administrador.'], 403);
        }

        try {
            // Soft-delete lógico: marcar activo = 0
            $empleado->activo = 0;
            $empleado->save();

            return response()->json(['success' => true, 'message' => 'Empleado marcado como inactivo.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al desactivar el empleado.'], 500);
        }
    }
}
