<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    use HasFactory;

    protected $table = 'facturas';

    protected $fillable = [
        'numero',
        'venta_id',
        'fecha_emision',
        'cliente_nombre',
        'cliente_nit',
        'total',
        'impuestos',
        'forma_pago',
    ];

    public $timestamps = true;

    // Relación: una factura pertenece a una venta
    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }
}