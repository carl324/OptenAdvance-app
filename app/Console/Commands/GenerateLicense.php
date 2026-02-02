<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class GenerateLicense extends Command
{
    protected $signature = 'app:generate-license {--type=trial} {--days=3}';
    protected $description = 'Generate encrypted license file with start and end dates';

    public function handle()
    {
        $type = $this->option('type'); // trial | full
        $days = (int) $this->option('days'); // duración en días

        $path = storage_path('app/license');
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }

        $file = $path . '/license.lic';

        $machineHash = $this->machineHash();

        // --- Fechas ---
        $startAt = Carbon::now()->format('Y-m-d H:i:s');
        $endAt   = Carbon::now()->addDays($days)->format('Y-m-d H:i:s');

        // --- Payload + firma ---
        $payload   = implode('|', [$type, $machineHash, $startAt, $endAt]);
        $signature = hash_hmac('sha256', $payload, config('app.key'));
        $data      = $payload . '|' . $signature;

        // --- Cifrar ---
        $key    = hash('sha256', config('app.key'), true);
        $iv     = random_bytes(16);

        $cipher = openssl_encrypt(
            $data,
            'AES-256-CBC',
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );

        file_put_contents($file, $iv . $cipher);

        // --- Validar inmediatamente ---
        $raw     = file_get_contents($file);
        $iv2     = substr($raw, 0, 16);
        $cipher2 = substr($raw, 16);

        $plain = openssl_decrypt(
            $cipher2,
            'AES-256-CBC',
            $key,
            OPENSSL_RAW_DATA,
            $iv2
        );

        $parts = explode('|', $plain);
        if (count($parts) === 5) {
            [$t, $hash, $sAt, $eAt, $sig] = $parts;
            $expected = hash_hmac('sha256', "$t|$hash|$sAt|$eAt", config('app.key'));

            $this->line(hash_equals($expected, $sig) ? 'FIRMA OK' : 'FIRMA INVALIDA');
            $this->line("Tipo: $t, Inicio: $sAt, Fin: $eAt");
        } else {
            $this->error('Error al generar licencia');
        }
    }

    private function machineHash(): string
    {
        $hostname = gethostname() ?: 'unknown';
        $mac = exec('getmac');
        $mac = preg_replace('/\s+/', '', $mac);

        $raw = $mac . $hostname . 'FIXED_SALT';
        return hash('sha256', $raw);
    }
}
