<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        // Tarea diaria a las 12:45 AM
        $schedule->command('app:test-cron')->everyMinute();
        $schedule->call(function () {
            file_get_contents('https://bookings.caribbeantransfers.tech/set/schedules');
        })->dailyAt('00:45');

        // Tarea cada 8 horas
        $schedule->call(function () {
            file_get_contents('https://bookings.caribbeantransfers.tech/set/processSchedulesForToday');
        })->everyEightHours();        
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
