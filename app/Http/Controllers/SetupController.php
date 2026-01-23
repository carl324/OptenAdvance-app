<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SetupController extends Controller
{
    public function show()
    {
        if (User::count() > 0) {
            return redirect('/login');
        }

        return view('setup.index');
    }

    public function store(Request $request)
    {
        if (User::count() > 0) {
            return redirect('/login');
        }

        $data = $request->validate([
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'password' => ['required', 'string', 'confirmed', 'min:6'],
        ]);

        User::create([
            'username' => $data['username'],
            'password' => Hash::make($data['password']),
            'role' => 'admin',
        ]);

        return redirect('/login');
    }
}
