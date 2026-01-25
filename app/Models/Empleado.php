<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

class Empleado extends User
{
    protected $table = 'users';

    /**
     * Booted: aplicar scope global para solo activos
     */
    protected static function booted()
    {
        static::addGlobalScope('activo', function (Builder $builder) {
            $builder->where('activo', 1);
        });
    }
}
