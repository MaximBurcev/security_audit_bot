<?php

namespace App\Telegram\Commands;

use App\Enums\ReportStatusEnum;
use App\Jobs\BotReportJob;
use App\Models\Audit;
use App\Models\BotMessage;
use App\Models\Report;
use App\Models\User;
use App\Services\BotMessageService;
use App\Services\ProjectService;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
use Telegram\Bot\Api;

class StartAuditCommand
{


    public function __construct(
        protected Nutgram $bot,
        protected ProjectService $projectService,
        protected BotMessageService $botMessageService
    ) {
    }

    public function handle(): void
    {
        // Создать отчеты через джобы. Создать аудит. Привязать отчеты к аудиту.


        $arAuditData = $this->getAuditData();

        Log::info('$arAuditData', $arAuditData);

        if (!empty($arAuditData)) {
            $busChain = [];
            foreach ($arAuditData as $arAuditDataItem) {
                $arReportData = [
                    'project_id' => $arAuditDataItem['projectId'],
                    'utility_id' => $arAuditDataItem['utilityId'],
                    'content'    => '',
                    'status'     => ReportStatusEnum::Created
                ];

                $report = Report::create($arReportData);

                $arBotReportJobData = [
                    'reportId'  => $report->id,
                    'utilityId' => $arReportData['utility_id'],
                    'projectId' => $arReportData['project_id'],
                    'chatId'    => $this->bot->chatId()
                ];
                $busChain[] = new BotReportJob($arBotReportJobData);

                $reportIds[] = $report->id;
            }

            Log::info('$reportIds', $reportIds);

            $arAuditCreateData = [
                'title'   => "Аудит №" . (Audit::count() + 1),
                'user_id' => User::where('telegram_user_id', '=', $userId)->get()->first()->id
            ];
            $audit = Audit::create($arAuditCreateData);
            if (!empty($reportIds)) {
                $audit->reports()->attach($reportIds);
            }
            $auditId = $audit->id;

            BotMessage::where('user_id', '=', $userId)->delete();

            $this->bot->sendMessage([
                'chat_id' => $this->bot->chatId(),
                'text'    => 'Аудит проектов запущен. Пожалуйста, ожидайте. ',
            ]);

            $batch = Bus::batch($busChain)->progress(function (Batch $batch) use ($chatId) {
                info('Batch', $batch->toArray());
                $telegram = new Api(config('telegram.bots.max_security_audit_bot.token'));
                $telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text'    => 'Аудит готов на ' . $batch->progress() . '%',
                ]);
            })->then(function (Batch $batch) use ($chatId, $auditId) {
                $telegram = new Api(config('telegram.bots.max_security_audit_bot.token'));
                info('$auditId', [$auditId]);
                $arReportLink = [];
                foreach (Audit::find($auditId)->reports as $report) {
                    $reportUrl = URL::signedRoute('public-report', ['report' => $report->id]);
                    $arReportLink[] = "<a href='{$reportUrl}'>№{$report->id}</a>";
                }

                $telegram->sendMessage([
                    'chat_id'    => $chatId,
                    'text'       => 'Ссылки на отчеты ' . implode(" ", $arReportLink),
                    'parse_mode' => 'HTML',
                ]);
                info('Batch', ["Аудит завершен"]);
            })->dispatch();

            info('Batch id', [$batch->id]);
        }
    }

    private function getAuditData(): array
    {
        $arAuditData = [];
        $arBotMessage = $this->botMessageService->getByUserId($this->bot->userId());
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

}
