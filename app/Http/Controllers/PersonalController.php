<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class PersonalController extends Controller
{
    public function index()
    {
        $query = User::where('role', '!=', 'admin');

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
}
