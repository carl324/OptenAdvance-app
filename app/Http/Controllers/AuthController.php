<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // POS local → NO recordar sesión
        $remember = $request->boolean('remember');

        if (!Auth::attempt($credentials, $remember)) {
            return back()
                ->withErrors(['auth' => 'Credenciales inválidas.'])
                ->withInput($request->only('email'));
        }

        
        if (!Auth::user()->activo) {
            Auth::logout();

            return back()->withErrors([
                'auth' => 'Usuario eliminado.'
            ]);
        }

        $request->session()->regenerate();

        return redirect('/ventas/nueva');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
