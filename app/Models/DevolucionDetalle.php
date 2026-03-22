<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DevolucionDetalle extends Model
{
    protected $table = 'devoluciones_detalle';
    protected $fillable = [
        'devolucion_id', 'venta_detalle_id', 'producto_id',
        'cantidad_devuelta', 'precio_unitario', 'subtotal'
    ];
protected $casts = [
    'cantidad_devuelta' => 'float',
    'precio_unitario'   => 'float',
    'subtotal'          => 'float',
];
    public function devolucion()
    {
        return $this->belongsTo(Devolucion::class);
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    public function ventaDetalle()
    {
        return $this->belongsTo(VentaDetalle::class);
    }
}