<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
| Pengingat deadline & perhitungan ulang prioritas dijalankan otomatis
| tiap 10 menit oleh Laravel Scheduler. Aktifkan dengan:
|   php artisan schedule:work          (saat development)
|   * * * * * php artisan schedule:run (cron di server)
*/
Schedule::command('reminders:send')->everyTenMinutes()->withoutOverlapping();
