<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Empleado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class PersonalController extends Controller
{
    public function index()
    {
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
                Rule::unique('users', 'email')->whereNull('deleted_at'),
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
                Rule::unique('users', 'email')->ignore($empleado->id)->whereNull('deleted_at'),
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

    $empleado->email = null;
    $empleado->save();
    
    $empleado->delete();

    return response()->json(['success' => true, 'message' => 'Empleado eliminado correctamente.']);
}

    public function updateAdminProfile(Request $request)
    {
        $admin = Auth::user();
        
        if (!$admin || $admin->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'No autorizado.'], 403);
        }

        $rules = [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($admin->id)->whereNull('deleted_at'),
            ],
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
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        $admin->name = $data['name'];
        $admin->email = $data['email'];
        
        if (!empty($data['password'])) {
            $admin->password = Hash::make($data['password']);
        }
        
        $admin->save();

        return response()->json([
            'success' => true,
            'message' => 'Perfil actualizado correctamente.',
            'user' => [
                'id' => $admin->id,
                'name' => $admin->name,
                'email' => $admin->email,
            ],
        ]);
    }
}