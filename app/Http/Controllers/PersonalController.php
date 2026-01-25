<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Empleado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;

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
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $empleado = User::create([
            'name' => $data['name'],
            'username' => $data['email'],
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

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($empleado->id),
            ],
            'phone' => ['nullable', 'string', 'max:50'],
        ]);

        // Evitar cambios peligrosos (no permitir cambiar role aquí)
        $empleado->name = $data['name'];
        $empleado->email = $data['email'];
        $empleado->username = $data['email'];
        $empleado->phone = $data['phone'] ?? null;
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
