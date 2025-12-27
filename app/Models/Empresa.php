<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $table = 'empresa';

    protected $fillable = [
        'nombre','nit','direccion','telefono','email','moneda'
    ];

    public $timestamps = true;
}
