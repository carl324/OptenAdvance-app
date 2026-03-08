<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Devolucion extends Model
{
    protected $table = 'devoluciones';
    protected $fillable = [
        'venta_id', 'user_id', 'motivo_devolucion_id',
        'observacion', 'metodo_reembolso',
        'monto_calculado', 'monto_real', 'fecha'
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function motivo()
    {
        return $this->belongsTo(MotivoDevolucion::class, 'motivo_devolucion_id');
    }

    public function detalles()
    {
        return $this->hasMany(DevolucionDetalle::class);
    }
}