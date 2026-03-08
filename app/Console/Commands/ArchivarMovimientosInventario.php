<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ArchivarMovimientosInventario extends Command
{
    protected $signature = 'app:archivar-movimientos';
    protected $description = 'Archiva movimientos de inventario con más de 3 meses';

    public function handle()
    {
        $fecha = Carbon::now()->subMonths(3);
        $total = 0;

        DB::table('inventario_movimientos')
            ->where('created_at', '<', $fecha)
            ->orderBy('id')
            ->chunk(500, function ($movimientos) use (&$total) {
                $ids = $movimientos->pluck('id')->toArray();

                // Insertar en archivo
                $insertar = $movimientos->map(fn($m) => [
                    'id_original'   => $m->id,
                    'producto_id'   => $m->producto_id,
                    'tipo'          => $m->tipo,
                    'cantidad'      => $m->cantidad,
                    'origen'        => $m->origen,
                    'referencia_id' => $m->referencia_id,
                    'descripcion'   => $m->descripcion,
                    'user_id'       => $m->user_id,
                    'created_at'    => $m->created_at,
                    'updated_at'    => $m->updated_at,
                    'archivado_at'  => now(),
                ])->toArray();

                DB::table('inventario_movimientos_archivo')->insertOrIgnore($insertar);

                // Eliminar originales
                DB::table('inventario_movimientos')->whereIn('id', $ids)->delete();

                $total += count($ids);
                $this->info("Archivados: {$total}");
            });

        $this->info("✅ Proceso completado. Total archivados: {$total}");
        return 0;
    }
}