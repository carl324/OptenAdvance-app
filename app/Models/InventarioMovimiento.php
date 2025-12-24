<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class InventarioMovimiento extends Model
{
    protected $table = 'inventario_movimientos';

    protected $fillable = [
        'producto_id',
        'tipo',
        'cantidad',
        'origen',
        'referencia_id',
        'descripcion',
    ];

    public $timestamps = true;

    public static function entrada($productoId, $cantidad, $origen, $referenciaId = null, $descripcion = null)
    {
        DB::transaction(function () use ($productoId, $cantidad, $origen, $referenciaId, $descripcion) {

            self::create([
                'producto_id'   => $productoId,
                'tipo'          => 'entrada',
                'cantidad'      => $cantidad,
                'origen'        => $origen,
                'referencia_id' => $referenciaId,
                'descripcion'   => $descripcion,
            ]);

            DB::table('productos')
                ->where('id', $productoId)
                ->increment('stock', $cantidad);
        });
    }

    public static function salida($productoId, $cantidad, $origen, $referenciaId = null, $descripcion = null)
    {
        DB::transaction(function () use ($productoId, $cantidad, $origen, $referenciaId, $descripcion) {

            self::create([
                'producto_id'   => $productoId,
                'tipo'          => 'salida',
                'cantidad'      => $cantidad,
                'origen'        => $origen,
                'referencia_id' => $referenciaId,
                'descripcion'   => $descripcion,
            ]);

            DB::table('productos')
                ->where('id', $productoId)
                ->decrement('stock', $cantidad);
        });
    }
}
