<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\DB;

class SuperAdminController extends Controller
{
    // Mostrar login
    public function showLogin()
    {
        if (Auth::check() && Auth::user()->role === 'super_admin') {
            return redirect()->route('superadmin.recovery');
        }

        if (Auth::check()) {
            Auth::logout();
        }

        return view('superadmin.login');
    }

    // Procesar login
    public function login(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || $user->role !== 'super_admin') {
        return back()->withErrors(['email' => 'Credenciales inválidas']);
    }

    if (!Hash::check($request->password, $user->password)) {
        return back()->withErrors(['email' => 'Credenciales inválidas']);
    }

    if (!$user->activo) {
        return back()->withErrors(['email' => 'Usuario desactivado']);
    }
    
    Auth::login($user); 
    $request->session()->put('super_admin_login_time', now());
    
    return redirect()->route('superadmin.recovery'); 
}
    // Mostrar panel
    public function showRecovery()
    {
        $users = User::where('role', '!=', 'super_admin')
            ->where('activo', 1)
            ->orderBy('role')
            ->orderBy('name')
            ->get();

        return view('superadmin.recovery', compact('users'));
    }

    // Resetear contraseña
    public function resetPassword(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'new_password' => ['required', 'confirmed', 'min:4'],
        ]);

        $targetUser = User::findOrFail($request->user_id);

        
        

        $targetUser->password = Hash::make($request->new_password);
        $targetUser->save();

        Log::warning('Password reset por Super Admin', [
            'super_admin_id' => Auth::id(),
            'target_user_id' => $targetUser->id,
            'target_email' => $targetUser->email,
            'ip' => $request->ip(),
        ]);

        return back()->with('success', "Contraseña de {$targetUser->email} reseteada exitosamente");
    }

    // Logout
    public function logout(Request $request)
{
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/login')
        ->with('success', 'Puede iniciar sesión normalmente');
}
public function markRevealed(Request $request)
{
    if (Auth::user()->role !== 'admin') {
        abort(403);
    }

    DB::table('super_admin_reveal')
        ->where('revealed', false)
        ->update(['revealed' => true]);

    Log::info('Super Admin credentials revealed', [
        'admin_id' => Auth::id(),
        'ip' => $request->ip()
    ]);

    return redirect()->back()->with('success', 'Credenciales guardadas');
}
}