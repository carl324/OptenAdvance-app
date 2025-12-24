<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'productos';

    protected $fillable = [
        'nombre',
        'precio',
        'stock',
        'activo',
    ];

    protected $casts = [
        'precio' => 'float',
        'stock'  => 'integer',
        'activo' => 'boolean',
    ];

    // Scope: solo activos
    public function scopeActivos($query)
    {
        return $query->where('activo', 1);
    }

    // Normalizar nombre del producto
    public function setNombreAttribute($value)
    {
        $this->attributes['nombre'] = trim(
            mb_convert_case($value, MB_CASE_TITLE, 'UTF-8')
        );
    }

    // Relación con movimientos de inventario
    public function movimientos()
    {
        return $this->hasMany(MovimientoInventario::class, 'producto_id');
    }
}