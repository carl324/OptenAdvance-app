<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VentaSeeder extends Seeder
{
    public function run(): void
    {
        $productos     = DB::table('productos')->select('id', 'precio_venta', 'precio_compra')->get()->toArray();
        $clientesIds   = DB::table('clientes')->pluck('id')->toArray();
        $cajasIds      = DB::table('cajas')->pluck('id')->toArray();
        $usuariosIds   = DB::table('users')->whereIn('role', ['admin', 'empleado'])->pluck('id')->toArray();

        if (empty($cajasIds) || empty($productos)) {
            $this->command->error('Necesitas cajas y productos antes de correr este seeder.');
            return;
        }

        $formasPago  = ['efectivo', 'tarjeta', 'transferencia', 'credito'];
        $totalVentas = 1000000;
        $lote        = 500;

        $ventasBatch   = [];
        $detallesBatch = [];
        $ventaId       = DB::table('ventas')->max('id') + 1;
        $insertadas    = 0;

        for ($i = 0; $i < $totalVentas; $i++) {
            $formaPago = $formasPago[array_rand($formasPago)];
            $estado    = $formaPago === 'credito' ? 'credito' : 'completada';
            $clienteId = rand(0, 3) === 0 ? null : $clientesIds[array_rand($clientesIds)];
            $cajaId    = $cajasIds[array_rand($cajasIds)];
            $userId    = $usuariosIds[array_rand($usuariosIds)];
            $fecha     = date('Y-m-d H:i:s', rand(strtotime('-1 year'), time()));

            $cantProductos  = rand(1, 5);
            $indicesProductos = array_rand($productos, min($cantProductos, count($productos)));
            if (!is_array($indicesProductos)) $indicesProductos = [$indicesProductos];

            $total = 0;
            foreach ($indicesProductos as $idx) {
                $producto    = $productos[$idx];
                $cantidad    = rand(1, 10);
                $precio      = $producto->precio_venta;
                $subtotal    = $precio * $cantidad;
                $total      += $subtotal;

                $detallesBatch[] = [
                    'venta_id'        => $ventaId,
                    'producto_id'     => $producto->id,
                    'cantidad'        => $cantidad,
                    'precio_unitario' => $precio,
                    'precio_compra'   => $producto->precio_compra,
                    'subtotal'        => $subtotal,
                    'iva'             => 0,
                    'total_pagado'    => $subtotal,
                    'motivo_anulacion'=> null,
                ];
            }

            $ventasBatch[] = [
                'cliente'         => null,
                'cliente_id'      => $clienteId,
                'forma_pago'      => $formaPago,
                'total'           => $total,
                'saldo_pendiente' => $estado === 'credito' ? $total : 0,
                'estado'          => $estado,
                'motivo_anulacion'=> null,
                'fecha_anulacion' => null,
                'fecha'           => $fecha,
                'created_at'      => $fecha,
                'updated_at'      => $fecha,
                'caja_id'         => $cajaId,
                'user_id'         => $userId,
            ];

            $ventaId++;

            if (count($ventasBatch) >= $lote) {
                DB::table('ventas')->insert($ventasBatch);
                DB::table('ventas_detalle')->insert($detallesBatch);
                $ventasBatch   = [];
                $detallesBatch = [];
                $insertadas   += $lote;
                $this->command->info("Insertadas {$insertadas} de {$totalVentas}...");
            }
        }

        if (!empty($ventasBatch)) {
            DB::table('ventas')->insert($ventasBatch);
            DB::table('ventas_detalle')->insert($detallesBatch);
        }

        $this->command->info('1,000,000 ventas insertadas correctamente.');
    }
}