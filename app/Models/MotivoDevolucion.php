<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MotivoDevolucion extends Model
{
    use SoftDeletes;
    protected $table = 'motivos_devolucion';
    protected $fillable = ['nombre', 'activo'];

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }
}