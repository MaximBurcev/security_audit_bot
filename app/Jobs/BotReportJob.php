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
        protected array          $arBotReportJobData,
        protected UtilityService $utilityService,
        protected ProjectService $projectService,
        protected ReportService  $reportService,
    )
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $utility = $this->utilityService->get($this->arBotReportJobData['utilityId']);
        $project = $this->projectService->get($this->arBotReportJobData['projectId']);
        $url = $project->url;

        $this->reportService->update($this->arBotReportJobData['reportId'], [
            'status' => ReportStatusEnum::InProcess
        ]);

        $reportContent = shell_exec($utility->command . " " . parse_url($url, PHP_URL_HOST));

        Log::info(__CLASS__, ['command' => $utility->command, 'url' => $url]);

        $this->reportService->update($this->arBotReportJobData['reportId'], [
            'content' => $reportContent,
            'status'  => ReportStatusEnum::Finished
        ]);


    }
}
