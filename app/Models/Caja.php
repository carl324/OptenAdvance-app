<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Caja extends Model
{
    protected $table = 'cajas';

    protected $fillable = [
        'user_id',
        'fecha_apertura',
        'monto_apertura',
        'nota_apertura',
        'fecha_cierre',
        'total_ventas',
        'total_efectivo',
        'monto_cierre_calculado',
        'monto_cierre_real',
        'diferencia',
        'nota_cierre',
        'estado',
    ];

    protected $casts = [
        'fecha_apertura' => 'datetime',
        'fecha_cierre' => 'datetime',
        'monto_apertura' => 'decimal:2',
        'total_ventas' => 'decimal:2',
        'total_efectivo' => 'decimal:2',
        'monto_cierre_calculado' => 'decimal:2',
        'monto_cierre_real' => 'decimal:2',
        'diferencia' => 'decimal:2',
    ];

    public function ventas()
    {
        return $this->hasMany(Venta::class, 'caja_id');
    }
}
