<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Empleado extends User
{
    use SoftDeletes;  // ← agrega esto

    protected $table = 'users';

    protected static function booted()
    {
        static::addGlobalScope('activo', function (Builder $builder) {
            $builder->where('activo', 1);
        });

        // Opcional: combina scopes si quieres mantener activo + softdelete
        // static::addGlobalScope('soft', function (Builder $builder) {
        //     $builder->whereNull('deleted_at');
        // });
    }
}