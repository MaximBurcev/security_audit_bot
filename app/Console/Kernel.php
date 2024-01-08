<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use function Sodium\compare;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('inspire')->everyMinute()->sendOutputTo(storage_path('logs/inspire.log'), true);

        $schedule->command('app:clear-all-cache')->daily()->sendOutputTo(storage_path('logs/clear-all-cache.log'), true);

        $schedule->command('app:cache:warmup')->dailyAt('07:00')->sendOutputTo(storage_path('logs/app.cache.warmup.log'), true);
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
