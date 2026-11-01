<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Collects infrastructure/DB health metrics into cache + history table
Schedule::command('health:collect-metrics')
    ->everyFiveMinutes()
    ->withoutOverlapping();

// Heartbeat so Health/Queue Monitoring can report a real last-scheduler-run time
Schedule::call(fn () => Cache::put('health_monitoring.scheduler_heartbeat', now()))
    ->everyMinute();
