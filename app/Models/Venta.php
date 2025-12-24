<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $table = 'ventas';
    
    // timestamps activados (es el default, pero lo dejamos explícito)
    public $timestamps = true;

    protected $fillable = [
        'cliente',
        'total',
        'estado',
        'fecha',
    ];

    protected $casts = [
        'total' => 'float',
        'fecha' => 'datetime',
    ];

    public function detalles()
    {
        return $this->hasMany(VentaDetalle::class, 'venta_id');
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