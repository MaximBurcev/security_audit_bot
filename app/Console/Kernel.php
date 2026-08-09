<?php

namespace App\Console;

use App\Models\Task;
use Cron\CronExpression;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('app:clear-all-cache')->daily()->sendOutputTo(storage_path('logs/clear-all-cache.log'),
            true);

        $schedule->command('app:cache:warmup')->dailyAt('03:00')->sendOutputTo(storage_path('logs/app.cache.warmup.log'),
            true);

        $schedule->command('app:projects.sync')->dailyAt('03:30')->withoutOverlapping()
            ->sendOutputTo(storage_path('logs/app.projects.sync.log'), true);

        $tasks = Task::query()->get();
        foreach ($tasks as $task) {
            // Планировщик разбирает cron уже при вычислении «пора ли запускать», и невалидное
            // выражение выбрасывает исключение из schedule:run целиком — вместе со всеми
            // остальными задачами. Одна битая запись не должна глушить расписание.
            if (!is_string($task->cron_format) || !CronExpression::isValidExpression($task->cron_format)) {
                Log::warning('Задача пропущена: некорректное cron-выражение', [
                    'task_id'     => $task->id,
                    'cron_format' => $task->cron_format,
                ]);

                continue;
            }

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
