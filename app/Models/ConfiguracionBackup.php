<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfiguracionBackup extends Model
{
    protected $table = 'backup_configuracion';
    
    protected $fillable = [
        'carpeta_destino',
        'prefijo_nombre_archivo',
        'frecuencia',
        'hora_backup',
        'retencion',
        'ultima_fecha_backup'
    ];
    
    protected $casts = [
        'ultima_fecha_backup' => 'datetime'
    ];
}