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
        // Ejecutar el comando de backup cada hora
        // El comando internamente verificará si debe ejecutarse según la configuración
        $schedule->command('backup:automatico')
                 ->hourly()
                 ->withoutOverlapping()
                 ->runInBackground();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}