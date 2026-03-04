<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule abandoned carts check daily at 9 AM
Schedule::command('carts:check-abandoned --hours=24')
    ->dailyAt('09:00')
    ->timezone('Asia/Qatar')
    ->description('Check for abandoned carts and notify admins');
