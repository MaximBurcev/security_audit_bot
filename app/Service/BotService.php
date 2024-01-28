<?php

namespace App\Service;

use App\Enums\ReportStatusEnum;
use App\Jobs\BotReportJob;
use App\Models\Audit;
use App\Models\BotMessage;
use App\Models\Project;
use App\Models\Report;
use App\Models\User;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Keyboard\Keyboard;

class BotService
{
    public static function handle(Request $request, $bot)
    {
        $arRequest = $request->toArray();
        Log::info('request', $request->toArray());
        if (array_key_exists('callback_query', $arRequest)) {

            $strData = $arRequest['callback_query']['data'];
            Log::info('data', [$strData]);

            $userId = $arRequest['callback_query']['from']['id'];
            $chatId = $arRequest['callback_query']['message']['chat']['id'];
            Log::info('chat id', [$chatId]);


            if (str_contains($strData, 'project')) {

                BotMessage::create([
                    'user_id' => $userId,
                    'data'    => $strData
                ]);

                $arUtility = [];
                foreach (Utility::all() as $utility) {
                    $arUtility[] = Keyboard::button([
                        'text'          => $utility->title,
                        'callback_data' => 'utility:' . $utility->id,
                    ]);
                }

                $keyboard = Keyboard::make()
                    ->inline()
                    ->row($arUtility);

                $bot->sendMessage([
                    'chat_id'      => $chatId,
                    'text'         => 'Выберите утилиту:',
                    'reply_markup' => $keyboard
                ]);
            } elseif (str_contains($strData, 'utility')) {

                BotMessage::create([
                    'user_id' => $userId,
                    'data'    => $strData
                ]);

                $arWhatNext[] = Keyboard::button([
                    'text'          => "Выбрать еще",
                    'callback_data' => 'more',
                ]);
                $arWhatNext[] = Keyboard::button([
                    'text'          => "Начать аудит",
                    'callback_data' => 'startAudit',
                ]);
                $keyboard = Keyboard::make()
                    ->inline()
                    ->row($arWhatNext);

                $bot->sendMessage([
                    'chat_id'      => $chatId,
                    'text'         => 'Что дальше?',
                    'reply_markup' => $keyboard
                ]);
            } elseif (str_contains($strData, 'more')) {
                $arProject = [];
                foreach (Project::all() as $project) {
                    $arProject[] = Keyboard::button([
                        'text'          => $project->title,
                        'callback_data' => 'project:' . $project->id,
                    ]);
                }

                $keyboard = Keyboard::make()
                    ->inline()
                    ->row($arProject);

                $bot->sendMessage([
                    'chat_id'      => $chatId,
                    'text'         => 'Выберите проект:',
                    'reply_markup' => $keyboard
                ]);
            } elseif (str_contains($strData, 'startAudit')) {


                // Создать отчеты через джобы. Создать аудит. Привязать отчеты к аудиту.
                $arAuditData = [];
                $arBotMessage = BotMessage::where('user_id', '=', $userId)->get()->all();
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

                Log::info('$arAuditData', $arAuditData);

                if (!empty($arAuditData)) {
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
                            'chatId'    => $chatId
                        ];
                        BotReportJob::dispatch($arBotReportJobData);

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
                }

                BotMessage::where('user_id', '=', $userId)->delete();

                $bot->sendMessage([
                    'chat_id' => $chatId,
                    'text'    => 'Аудит проектов запущен. Ожидайте ссылки на отчеты',
                ]);
            }


        } else {
            $chatId = $arRequest['message']['chat']['id'];
            Log::info('no callback_query', ['chat_id' => $chatId]);

        }
    }
}
