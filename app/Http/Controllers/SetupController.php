<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SetupController extends Controller
{
    public function show()
    {
        // Si ya existe un administrador, redirigir al login
        if (User::where('role', 'admin')->exists()) {
            return redirect('/login');
        }

        return view('setup.index');
    }

    public function store(Request $request)
    {
        // Evitar doble registro de administrador
        if (User::where('role', 'admin')->exists()) {
            return redirect('/login');
        }

        // Validación controlada en el servidor con mensajes personalizados (en español)
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:3'],
        ];

        $messages = [
            'name.required' => 'El nombre es obligatorio.',
            'name.string' => 'El nombre debe ser texto.',
            'name.max' => 'El nombre no puede tener más de :max caracteres.',

            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe tener un formato válido.',
            'email.max' => 'El correo electrónico no puede tener más de :max caracteres.',
            'email.unique' => 'Este correo ya está registrado.',

            'password.required' => 'La contraseña es obligatoria.',
            'password.string' => 'La contraseña debe ser texto.',
            'password.min' => 'La contraseña debe tener al menos :min caracteres.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();

        try {
            $user = null;
            DB::transaction(function() use ($data, &$user) {
                // Re-check inside transaction to avoid race conditions
                if (User::where('role', 'admin')->exists()) {
                    throw new \RuntimeException('Administrator already exists');
                }

                $user = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make($data['password']),
                    'role' => 'admin',
                ]);
            });

            // Login the newly created admin and redirect to dashboard
            if ($user) {
                Auth::login($user);
                return redirect('/productos');
            }

            return redirect('/login');
        } catch (\Throwable $e) {
            // Do not expose internal error details or passwords
            return back()->withErrors(['setup' => 'No se pudo crear el administrador.']);
        }
    }
}
