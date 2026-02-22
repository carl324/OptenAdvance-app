<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('licencia:verificar')
    ->daily()
    ->withoutOverlapping();

Schedule::command('backup:automatico')
    ->everyminute()
    ->withoutOverlapping();

Schedule::command('disco:verificar')
    ->weekly()
    ->withoutOverlapping();