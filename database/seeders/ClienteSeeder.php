<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        $nombres = ['Carlos','María','Juan','Ana','Pedro','Laura','Luis','Sandra','Jorge','Diana','Andrés','Paola','Felipe','Natalia','Ricardo','Valentina','Sergio','Camila','David','Juliana'];
        $apellidos = ['García','Rodríguez','Martínez','López','González','Pérez','Sánchez','Ramírez','Torres','Flores','Rivera','Gómez','Díaz','Morales','Muñoz','Vargas','Herrera','Jiménez','Castro','Romero'];
        $cupos = [null, -1, 500000, 1000000, 2000000];

        $clientes = [];
        for ($i = 1; $i <= 500; $i++) {
            $nombre = $nombres[array_rand($nombres)] . ' ' . $apellidos[array_rand($apellidos)];
            $clientes[] = [
                'nombre'          => $nombre . ' ' . $i,
                'telefono'        => '3' . rand(100000000, 999999999),
                'email'           => 'cliente' . $i . '@test.com',
                'nit'             => rand(100000000, 999999999),
                'direccion'       => 'Calle ' . rand(1, 100) . ' # ' . rand(1, 50) . '-' . rand(1, 99),
                'cupo_credito'    => $cupos[array_rand($cupos)],
                'saldo_pendiente' => 0,
                'created_at'      => now(),
                'updated_at'      => now(),
            ];
        }

        DB::table('clientes')->insert($clientes);
    }
}