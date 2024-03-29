<?php

namespace App\Jobs;

use App\Enums\ReportStatusEnum;
use App\Models\Project;
use App\Models\Report;
use App\Models\Utility;
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

    private mixed $arBotReportJobData;

    /**
     * Create a new job instance.
     */
    public function __construct($arBotReportJobData)
    {
        $this->arBotReportJobData = $arBotReportJobData;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $utility = Utility::query()->findOrFail($this->arBotReportJobData['utilityId']);
        $project = Project::query()->findOrFail($this->arBotReportJobData['projectId']);
        $report = Report::query()->findOrFail($this->arBotReportJobData['reportId']);
        $url = $project->url;

        $report->updateOrFail([
            'status' => ReportStatusEnum::InProcess
        ]);
        $reportContent = shell_exec($utility->command . " " . parse_url($url, PHP_URL_HOST));
        Log::info(__CLASS__, ['command' => $utility->command, 'url' => $url]);
        $report->updateOrFail([
            'content' => $reportContent,
            'status'  => ReportStatusEnum::Finished
        ]);


    }
}
