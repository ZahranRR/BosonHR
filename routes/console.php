<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Log;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Artisan::command('attendance:daily-recap', function () {
//     try {
//         $controller = new \App\Http\Controllers\Superadmin\AttandanceController();
//         $controller->dailyRecap();
//         $this->info('Daily attendance recap completed successfully.');
//     } catch (\Exception $e) {
//         Log::error('Error running daily-recap command', ['error' => $e->getMessage()]);
//         $this->error('An error occurred. Check logs for details.');
//     }
// })->purpose('Generate daily attendance recap for all employees');

// // Schedule the command
// app(Schedule::class)->command('attendance:daily-recap')->dailyAt('23:59');