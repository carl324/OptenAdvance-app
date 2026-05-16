<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class LicenseDays extends Command
{
    protected $signature = 'license:days';
    protected $description = 'Muestra los días restantes de la licencia';

    private const MASTER_KEY = '145537332a7bf08db92cb37b3b752588c127fbb85959b5f577ab70b08d154956';

    public function handle()
    {
        $file = storage_path('app/license/license.lic');

        if (!file_exists($file)) {
            $this->error(' SIN LICENCIA');
            return 1;
        }

        $raw = @file_get_contents($file);
        
        if ($raw === false || strlen($raw) < 17) {
            $this->error(' ARCHIVO DE LICENCIA CORRUPTO O VACÍO');
            return 1;
        }

        $key = hash('sha256', self::MASTER_KEY, true);
        $iv = substr($raw, 0, 16);
        $cipher = substr($raw, 16);

        $plain = openssl_decrypt(
            $cipher,
            'AES-256-CBC',
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );

        if ($plain === false) {
            $this->error('LICENCIA CORRUPTA O INVÁLIDA');
            return 1;
        }

        $parts = explode('|', $plain);
        
        if (count($parts) !== 5) {
            $this->error('FORMATO DE LICENCIA INVÁLIDO');
            return 1;
        }

        [$type, $machineHash, $startAt, $endAt, $sig] = $parts;

        // Validar firma
        $expected = hash_hmac(
            'sha256',
            $type . '|' . $machineHash . '|' . $startAt . '|' . $endAt,
            self::MASTER_KEY
        );

        if (!hash_equals($expected, $sig)) {
            $this->error('❌ FIRMA DE LICENCIA INVÁLIDA');
            return 1;
        }

        $now = Carbon::now();
        $endDate = Carbon::parse($endAt);
        $startDate = Carbon::parse($startAt);
        
        $daysRemaining = (int) ceil($now->diffInDays($endDate, false));
        $totalDays = (int) $startDate->diffInDays($endDate);

        // Determinar estado
        if ($daysRemaining < 0) {
            $this->error("❌ LICENCIA VENCIDA");
            $this->line("   Expiró hace: " . abs($daysRemaining) . " días");
        } elseif ($daysRemaining <= 7) {
            $this->warn(" LICENCIA POR VENCER");
            $this->line("   Días restantes: {$daysRemaining}");
        } else {
            $this->info("✓ LICENCIA ACTIVA");
            $this->line("   Días restantes: {$daysRemaining}");
        }

        // Info adicional
        $this->newLine();
        $this->line("Tipo:        " . strtoupper($type));
        $this->line("Inicio:      " . $startDate->format('d/m/Y H:i'));
        $this->line("Vencimiento: " . $endDate->format('d/m/Y H:i'));
        $this->line("Duración:    {$totalDays} días");

        return 0;
    }
}