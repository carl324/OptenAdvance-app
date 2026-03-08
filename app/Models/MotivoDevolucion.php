<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MotivoDevolucion extends Model
{
    protected $table = 'motivos_devolucion';
    protected $fillable = ['nombre', 'activo'];

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}