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
        'user_id',
    ];

    public $timestamps = true;

    /**
     * Relación: Un movimiento pertenece a un producto
     */
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }

    public static function entrada($productoId, $cantidad, $origen, $referenciaId = null, $descripcion = null, $userId = null)
    {
        // Envolver en transacción para garantizar consistencia atómica:
        // Si create() falla → no se modifica stock
        // Si increment() falla → se revierte el create()
        return DB::transaction(function () use ($productoId, $cantidad, $origen, $referenciaId, $descripcion, $userId) {
            self::create([
                'producto_id'   => $productoId,
                'tipo'          => 'entrada',
                'cantidad'      => $cantidad,
                'origen'        => $origen,
                'referencia_id' => $referenciaId,
                'descripcion'   => $descripcion,
                'user_id'       => $userId,
            ]);

            DB::table('productos')
                ->where('id', $productoId)
                ->increment('stock', $cantidad);
        });
    }

    public static function salida($productoId, $cantidad, $origen, $referenciaId = null, $descripcion = null, $userId = null)
    {
        // Envolver en transacción para garantizar consistencia atómica:
        // Validación + decremento + movimiento deben ser indivisibles
        // Si cualquier paso falla → todo se revierte
        return DB::transaction(function () use ($productoId, $cantidad, $origen, $referenciaId, $descripcion, $userId) {
            // Validación atómica: verificar stock y decrementar en una operación
            // La cláusula where+decrement es atómica en la BD
            $actualizados = DB::table('productos')
                ->where('id', $productoId)
                ->where('stock', '>=', $cantidad)  // Asegurar que hay stock suficiente
                ->decrement('stock', $cantidad);

            // Si no se actualizó ninguna fila, significa que no hay stock suficiente
            if ($actualizados === 0) {
                throw new \Exception("No hay suficiente stock para el producto ID {$productoId}. Intenta realizar una salida de {$cantidad} unidades.");
            }

            // Solo registrar el movimiento si el decremento fue exitoso
            self::create([
                'producto_id'   => $productoId,
                'tipo'          => 'salida',
                'cantidad'      => $cantidad,
                'origen'        => $origen,
                'referencia_id' => $referenciaId,
                'descripcion'   => $descripcion,
                'user_id'       => $userId,
            ]);
        });
    }
}
