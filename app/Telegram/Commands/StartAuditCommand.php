<?php

namespace App\Telegram\Commands;

use App\Enums\ReportStatusEnum;
use App\Jobs\BotReportJob;
use App\Models\Audit;
use App\Models\BotMessage;
use App\Models\Report;
use App\Models\User;
use App\Services\AuditService;
use App\Services\BotMessageService;
use App\Services\ProjectService;
use App\Services\ReportService;
use App\Services\UserService;
use App\Services\UtilityService;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Nutgram\Laravel\Facades\Telegram;
use SergiX44\Nutgram\Nutgram;
use function Laravel\Prompts\text;

class StartAuditCommand
{


    private array $busChain;

    public function __construct(
        protected Nutgram           $bot,
        protected ProjectService    $projectService,
        protected BotMessageService $botMessageService,
        protected ReportService     $reportService,
        protected AuditService      $auditService,
        protected UserService       $userService,
        protected UtilityService    $utilityService,
    )
    {
    }

    public function handle(): void
    {

        $arAuditData = $this->getAuditData();

        Log::info('$arAuditData', $arAuditData);

        if (!empty($arAuditData)) {
            $audit = $this->createAudit($arAuditData);
            $this->runAudit($audit);
        }
    }

    private function getAuditData(): array
    {
        $arAuditData = [];
        $arBotMessage = $this->botMessageService->getByUserId($this->userService->getByTelegramId($this->bot->userId())->id);
        for ($i = 0; $i < count($arBotMessage); $i += 2) {
            Log::info('$arBotMessage', [$arBotMessage[$i]]);
            $projectIndex = $i;
            $utilityIndex = $i + 1;
            $projectId = last(explode(":", $arBotMessage[$projectIndex]->data));
            $utilityId = last(explode(":", $arBotMessage[$utilityIndex]->data));
            $arAuditData[] = [
                'projectId' => $projectId,
                'utilityId' => $utilityId
            ];
        }

        return $arAuditData;
    }

    private function createAudit($arAuditData): Audit
    {
        foreach ($arAuditData as $arAuditDataItem) {


            $report = $this->reportService->create($arAuditDataItem);


            $arBotReportJobData = [
                'reportId'  => $report->id,
                'utilityId' => $report->utility_id,
                'projectId' => $report->project_id,
                'chatId'    => $this->bot->chatId()
            ];
            $this->busChain[] = new BotReportJob($arBotReportJobData, $this->utilityService, $this->projectService, $this->reportService);

            $reportIds[] = $report->id;
        }

        Log::info('$reportIds', $reportIds);

        $audit = $this->auditService->create([
            'title'   => "Аудит №" . $this->auditService->getCount(),
            'user_id' => $this->userService->getByTelegramId($this->bot->userId())->id,
        ]);
        if (!empty($reportIds)) {
            $audit->reports()->attach($reportIds);
        }


        $this->botMessageService->deleteByUserId($this->userService->getByTelegramId($this->bot->userId())->id);

        $this->bot->sendMessage('Аудит проектов запущен. Пожалуйста, ожидайте.');

        return $audit;
    }

    private function runAudit($audit): void
    {
        $auditId = $audit->id;

        $chatId = $this->bot->chatId();

        info('runAudit', ['chatId' => $chatId, 'auditId' => $auditId]);

        $batch = Bus::batch($this->busChain)->progress(function (Batch $batch) use ($chatId) {
            info('Batch', $batch->toArray());

            //Telegram::sendMessage('Аудит готов на ' . $batch->progress() . '%', $chatId);

        })->then(function (Batch $batch) use ($auditId, $chatId) {

            info('$auditId', [$auditId]);
            $arReportLink = [];
            foreach ($this->auditService->get($auditId)->reports as $report) {
                $reportUrl = URL::signedRoute('public-report', ['report' => $report->id]);
                $arReportLink[] = "<a href='{$reportUrl}'>№{$report->id}</a>";
            }

            //Telegram::sendMessage(text: 'Ссылки на отчеты ' . implode(" ", $arReportLink), chat_id: $chatId, parse_mode: 'html');

            info('Batch', ["Аудит завершен"]);

        })->dispatch();

        info('Batch id', [$batch->id]);
    }

}
