<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VentaDetalle extends Model
{
    protected $table = 'ventas_detalle';

    protected $fillable = [
        'venta_id',
        'producto_id',
        'cantidad',
        'precio_unitario',
        'precio_compra',      
        'iva',
        'subtotal',
        'total_pagado',
        'motivo_anulacion',   
    ];

    protected $casts = [
        'cantidad'        => 'integer',
        'precio_unitario' => 'float',
        'precio_compra'   => 'float',     
        'iva'             => 'float',
        'subtotal'        => 'float',
        'total_pagado'    => 'float',
    ];

    public $timestamps = false;

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}