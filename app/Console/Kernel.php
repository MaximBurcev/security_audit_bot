<?php

namespace App\Console;

use App\Models\Task;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('inspire')->everyMinute()->sendOutputTo(storage_path('logs/inspire.log'), true);

        $schedule->command('app:clear-all-cache')->daily()->sendOutputTo(storage_path('logs/clear-all-cache.log'),
            true);

        $schedule->command('app:cache:warmup')->dailyAt('07:00')->sendOutputTo(storage_path('logs/app.cache.warmup.log'),
            true);

        $tasks = Task::all();
        foreach ($tasks as $task) {
            $schedule->command('app:report.update',
                [$task->report_id])->cron($task->cron_format)->sendOutputTo(storage_path('logs/app.report.update.log'),
                true);
        }
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
