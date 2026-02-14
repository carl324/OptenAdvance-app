<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Verificar si ya existe
        if (User::where('role', 'super_admin')->exists()) {
            $this->command->warn('⚠️  Ya existe un super_admin');
            return;
        }

        // Generar contraseña aleatoria fuerte
        $password = $this->generarPassword();

        // Crear usuario
        User::create([
            'email' => 'superadmin@sistema.local',  // ← CAMBIO AQUÍ
            'password' => Hash::make($password),
            'role' => 'super_admin',
            'name' => 'Super Admin',
            'activo' => 1,
        ]);
        DB::table('super_admin_reveal')->insert([
    'email' => 'superadmin@sistema.local',
    'password' => Crypt::encryptString($password),
    'revealed' => false,
    'created_at' => now(),
    'updated_at' => now(),
]);

        // Mostrar contraseña
        $this->command->info('✅ Super Admin creado');
        $this->command->info('📧 Email: superadmin@sistema.local');
        $this->command->error('🔐 CONTRASEÑA: ' . $password);
        $this->command->warn('⚠️  GUARDA ESTA CONTRASEÑA - NO SE MOSTRARÁ DE NUEVO');
    }

    private function generarPassword(): string
    {
        $mayus = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $minus = 'abcdefghjkmnpqrstuvwxyz';
        $nums = '23456789';
        $simb = '!@#$%&*-_';

        $pass = '';
        $pass .= $mayus[rand(0, strlen($mayus) - 1)];
        $pass .= $mayus[rand(0, strlen($mayus) - 1)];
        $pass .= $minus[rand(0, strlen($minus) - 1)];
        $pass .= $minus[rand(0, strlen($minus) - 1)];
        $pass .= $nums[rand(0, strlen($nums) - 1)];
        $pass .= $nums[rand(0, strlen($nums) - 1)];
        $pass .= $simb[rand(0, strlen($simb) - 1)];
        $pass .= $simb[rand(0, strlen($simb) - 1)];

        $todos = $mayus . $minus . $nums . $simb;
        for ($i = 0; $i < 8; $i++) {
            $pass .= $todos[rand(0, strlen($todos) - 1)];
        }

        return str_shuffle($pass);
    } 
}