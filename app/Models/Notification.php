<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notifications';

    protected $fillable = [
        'modulo',
        'tipo',
        'titulo',
        'mensaje',
        'leida',
        'data',
    ];

    protected $casts = [
        'leida' => 'boolean',
        'data'  => 'array',
    ];

    // ============================================================
    // Helper estático para crear notificaciones desde cualquier lado
    // Uso: Notification::crear('backup', 'error', 'Backup fallido', 'Descripción')
    // ============================================================
    public static function crear(
        string $modulo,
        string $tipo,
        string $titulo,
        string $mensaje,
        array $data = []
    ): self {
        return self::create([
            'modulo'  => $modulo,
            'tipo'    => $tipo,
            'titulo'  => $titulo,
            'mensaje' => $mensaje,
            'leida'   => false,
            'data'    => !empty($data) ? $data : null,
        ]);
    }

    // Scope para no leídas
    public function scopeNoLeidas($query)
    {
        return $query->where('leida', false);
    }

    // Scope por módulo
    public function scopeModulo($query, string $modulo)
    {
        return $query->where('modulo', $modulo);
    }
}