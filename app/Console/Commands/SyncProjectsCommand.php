<?php

namespace App\Console\Commands;

use App\Services\ProjectSyncService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

class SyncProjectsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:projects.sync
                            {--dry-run : Показать, что изменится, ничего не записывая}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Синхронизировать проекты с отчётом Uptime Monitoring';

    /**
     * Execute the console command.
     */
    public function handle(ProjectSyncService $projectSyncService)
    {
        if ($this->option('dry-run')) {
            return $this->dryRun($projectSyncService);
        }

        try {
            $result = $projectSyncService->sync();
        } catch (Throwable $e) {
            Log::error('projects.sync failed', ['message' => $e->getMessage()]);
            $this->error('Синхронизация не удалась: ' . $e->getMessage());

            return self::FAILURE;
        }

        $this->info(sprintf(
            'Синхронизация завершена: в отчёте %d, создано %d, обновлено %d, пропущено %d',
            $result['total'],
            $result['created'],
            $result['updated'],
            count($result['skipped'])
        ));

        foreach ($result['skipped'] as $entry) {
            $this->line("  пропущено (не удалось определить хост): {$entry}");
        }

        return self::SUCCESS;
    }

    private function dryRun(ProjectSyncService $projectSyncService): int
    {
        try {
            $preview = $projectSyncService->preview();
        } catch (Throwable $e) {
            $this->error('Не удалось получить отчёт: ' . $e->getMessage());

            return self::FAILURE;
        }

        $this->info(sprintf(
            'В отчёте %d записей: будет создано %d, обновлено %d, пропущено %d',
            $preview['total'],
            count($preview['create']),
            count($preview['update']),
            count($preview['skipped'])
        ));

        foreach ($preview['create'] as $row) {
            $this->line("  + {$row['title']} -> {$row['url']}");
        }

        foreach ($preview['update'] as $row) {
            $this->line("  ~ {$row['title']} (было: {$row['old_title']})");
        }

        foreach ($preview['skipped'] as $entry) {
            $this->line("  - пропущено: {$entry}");
        }

        return self::SUCCESS;
    }
}
