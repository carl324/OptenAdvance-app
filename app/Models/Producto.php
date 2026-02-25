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
        'codigo_barras',
        'precio_compra',     
        'precio_venta',
        'stock',
        'activo',
        'iva',
        'precio_con_iva',
    ];

    protected $casts = [
        'precio_compra' => 'float',
        'precio_venta' => 'float',
        'stock'  => 'integer',
        'activo' => 'boolean',
        'iva' => 'float',
        'precio_con_iva' => 'float',
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

    // ========== ACCESSORS PARA GANANCIA ==========
    
    /**
     * Ganancia en pesos por unidad
     */
    public function getGananciaAttribute()
    {
        return $this->precio_venta - $this->precio_compra;
    }

    /**
     * Margen de ganancia en porcentaje
     */
    public function getMargenPorcentajeAttribute()
    {
        if ($this->precio_compra == 0) return 0;
        return (($this->precio_venta - $this->precio_compra) / $this->precio_compra) * 100;
    }

    // Relación con movimientos de inventario
    public function movimientos()
    {
        return $this->hasMany(MovimientoInventario::class, 'producto_id');
    }
}