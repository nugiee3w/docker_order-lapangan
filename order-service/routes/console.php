<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule the expired orders processing command
// Default: Complete paid orders, delete unpaid orders
Schedule::command('orders:process-expired')
    ->hourly()
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/expired-orders.log'));

// Alternative: Delete ALL expired orders (including paid ones)
// Uncomment the line below and comment the above if you want to delete all expired orders
// Schedule::command('orders:process-expired --delete-paid')
//     ->hourly()
//     ->withoutOverlapping()
//     ->runInBackground()
//     ->appendOutputTo(storage_path('logs/expired-orders-delete.log'));
