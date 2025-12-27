<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $table = 'ventas';

    public $timestamps = true;

    protected $fillable = [
        'cliente',
        'cliente_nombre',  // nuevo
        'cliente_nit',     // nuevo
        'numero_factura',  // nuevo
        'fecha_emision',   // nuevo
        'forma_pago',      // nuevo
        'total',
        'estado',
        'fecha',
    ];

    protected $casts = [
        'total' => 'float',
        'fecha' => 'datetime',
        'fecha_emision' => 'date',
    ];

    public function detalles()
    {
        return $this->hasMany(VentaDetalle::class, 'venta_id');
    }

    public function factura()
    {
        return $this->hasOne(Factura::class);
    }

    public function scopeCompletadas($query)
    {
        return $query->where('estado', 'completada');
    }

    public function scopeAnuladas($query)
    {
        return $query->where('estado', 'anulada');
    }
}