<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $login = $data['username'];

        // Determinar si el valor es un email o un username
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [$field => $login, 'password' => $data['password']];

        // Intentar autenticación; recordar sesión por defecto (cookie persistente)
        $remember = true;

        if (!Auth::attempt($credentials, $remember)) {
            return back()
                ->withErrors(['auth' => 'Credenciales inválidas.'])
                ->withInput($request->only('username'));
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
