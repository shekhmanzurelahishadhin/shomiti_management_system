<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Auto-generate bills on 1st of every month at 00:05
Schedule::command('bills:generate-monthly')->monthlyOn(1, '00:05');

// Apply late fines daily after due date
Schedule::command('bills:apply-fines')->dailyAt('00:10');
