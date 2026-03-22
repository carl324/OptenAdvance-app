<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\DevolucionDetalle;

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

public function recalcularSaldo(int $montoDevuelto): void
{
    if ($this->saldo_pendiente <= 0) return;

    $reduccion = min($montoDevuelto, $this->saldo_pendiente);

    $this->saldo_pendiente -= $reduccion;
    $this->save();

    if ($this->cliente_id) {
        \App\Models\Cliente::where('id', $this->cliente_id)
            ->decrement('saldo_pendiente', $reduccion);
    }
}

public function recalcularEstado(): void
{
    if ($this->estado === 'anulada') return;

    $this->loadMissing('detalles');

    $devuelto = DevolucionDetalle::whereHas('devolucion', fn($q) => $q->where('venta_id', $this->id))
        ->selectRaw('producto_id, SUM(cantidad_devuelta) as total_devuelto')
        ->groupBy('producto_id')
        ->pluck('total_devuelto', 'producto_id');

    if ($devuelto->isEmpty()) return;

    $lineasCubiertas = $this->detalles->filter(
        fn($d) => ($devuelto[$d->producto_id] ?? 0) >= $d->cantidad
    )->count();

    $this->estado = $lineasCubiertas === $this->detalles->count() ? 'devuelta' : 'dev_parcial';
    $this->save();
}
}