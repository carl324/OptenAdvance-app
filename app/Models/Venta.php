<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $table = 'ventas';

    public $timestamps = true;

    protected $fillable = [
        'cliente',
        'cliente_nombre',
        'cliente_nit',
        'numero_factura',
        'fecha_emision',
        'forma_pago',
        'total',
        'estado',
        'fecha',
        'user_id',
        'caja_id',
        'cliente_id',
        'saldo_pendiente',
    ];

    protected $casts = [
        'total'           => 'float',
        'fecha'           => 'datetime',
        'fecha_emision'   => 'date',
        'saldo_pendiente' => 'integer',
    ];

    public function detalles()
    {
        return $this->hasMany(VentaDetalle::class, 'venta_id');
    }

    public function factura()
    {
        return $this->hasOne(Factura::class);
    }

    public function caja()
    {
        return $this->belongsTo(Caja::class, 'caja_id');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function abonos()
    {
        return $this->hasMany(Abono::class);
    }

    public function scopeCompletadas($query)
    {
        return $query->where('estado', 'completada');
    }

    public function scopeAnuladas($query)
    {
        return $query->where('estado', 'anulada');
    }

    public function scopeCreditos($query)
    {
        return $query->where('estado', 'credito');
    }

    public function scopeParciales($query)
    {
        return $query->where('estado', 'parcial');
    }
}