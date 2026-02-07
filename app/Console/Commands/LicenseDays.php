<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class LicenseDays extends Command
{
    // Nombre del comando que vas a usar en consola
    protected $signature = 'license:days';
    protected $description = 'Muestra los días restantes de la licencia';

    public function handle()
    {
        $file = storage_path('app/license/license.lic');

        if (!file_exists($file)) {
            $this->error('SIN LICENCIA');
            return;
        }

        $key = hash('sha256', config('app.key'), true);
        $raw = file_get_contents($file);

        $iv = substr($raw, 0, 16);
        $cipher = substr($raw, 16);

        $plain = openssl_decrypt(
            $cipher,
            'AES-256-CBC',
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );

        if (!$plain) {
            $this->error('LICENCIA CORRUPTA');
            return;
        }

        // Extrae la fecha de fin de la licencia
        [, , , $endAt] = explode('|', $plain);

        $days = now()->diffInDays($endAt, false);

        $this->line("DÍAS RESTANTES: $days");
    }
}
