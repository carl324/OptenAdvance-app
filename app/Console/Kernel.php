<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{   protected $commands = [
        \App\Console\Commands\LicenseDays::class,
    ];
    protected function schedule(Schedule $schedule)
    {
        // NOTA: El backup automático se gestiona desde Windows Task Scheduler
        // Ver: C:\optenadvance\app\scripts\ejecutar-backup.bat
        // Tarea: "OptenBackupAutomatico" (cada minuto)

        // Verificar estado de licencia cada hora
        $schedule->command('licencia:verificar')->hourly();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}