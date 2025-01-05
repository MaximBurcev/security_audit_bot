<?php

namespace App\Jobs;

use App\Enums\ReportStatusEnum;
use App\Services\ProjectService;
use App\Services\ReportService;
use App\Services\UtilityService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class BotReportJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    /**
     * Create a new job instance.
     */
    public function __construct(
        protected array $arBotReportJobData,
    )
    {
    }

    /**
     * Execute the job.
     */
    public function handle(UtilityService $utilityService, ProjectService $projectService, ReportService $reportService): void
    {
        $utility = $utilityService->get($this->arBotReportJobData['utilityId']);
        $project = $projectService->get($this->arBotReportJobData['projectId']);
        $url = $project->url;

        $reportService->update($this->arBotReportJobData['reportId'], [
            'status' => ReportStatusEnum::InProcess
        ]);

        $reportContent = shell_exec($utility->command . " " . parse_url($url, PHP_URL_HOST));

        Log::info(__CLASS__, ['command' => $utility->command, 'url' => $url]);

        $reportService->update($this->arBotReportJobData['reportId'], [
            'content' => $reportContent,
            'status'  => ReportStatusEnum::Finished
        ]);


    }
}
