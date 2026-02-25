<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nombre',
        'telefono',
        'email',
        'nit',
        'direccion',
        'cupo_credito',
        'saldo_pendiente',
    ];

    protected $casts = [
        'cupo_credito'    => 'integer',
        'saldo_pendiente' => 'integer',
    ];

    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }

    public function abonos()
    {
        return $this->hasMany(Abono::class);
    }
}