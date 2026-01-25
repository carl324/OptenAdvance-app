<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'min:3', 'max:255', 'unique:users,username'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        try {
            $user = null;
            DB::transaction(function() use ($data, &$user) {
                // Re-check inside transaction to avoid race conditions
                if (User::where('role', 'admin')->exists()) {
                    throw new \RuntimeException('Administrator already exists');
                }

                $user = User::create([
                    'name' => $data['name'],
                    'username' => $data['username'],
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
