<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    protected $table = 'configuracion';
    protected $fillable = ['clave', 'valor', 'descripcion'];

    public static function get(string $clave, $default = null)
    {
        $config = static::where('clave', $clave)->first();
        return $config ? $config->valor : $default;
    }

    public static function set(string $clave, $valor): void
    {
        static::updateOrInsert(
            ['clave' => $clave],
            ['valor' => $valor, 'updated_at' => now()]
        );
    }
}