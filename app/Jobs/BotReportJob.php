<?php

namespace App\Jobs;

use App\Enums\ReportStatusEnum;
use App\Models\Project;
use App\Models\Report;
use App\Models\Utility;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Telegram\Bot\BotsManager;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Api;

class BotReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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

        $telegram = new Api(config('telegram.bots.max_security_audit_bot.token'));
        $telegram->sendMessage([
            'chat_id'    => $this->arBotReportJobData['chatId'],
            'text'       => 'Ссылка на отчет <a href="' . URL::signedRoute('public-report',
                    ['report' => $report->id]) . '">Здесь</a>',
            'parse_mode' => 'HTML',
        ]);
    }
}
