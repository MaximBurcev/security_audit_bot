<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\BotMessage;
use App\Models\Project;
use App\Models\Utility;
use App\Service\BotService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;
use Telegram\Bot\BotsManager;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;

class WebhookController extends Controller
{
    protected BotsManager $botsManager;
    private Api $bot;

    public function __construct(BotsManager $botsManager)
    {
        $this->botsManager = $botsManager;
        $this->bot = $botsManager->bot('max_security_audit_bot');
    }

    public function __invoke(Request $request): Response
    {
        $arRequest = $request->toArray();
        Log::info('request', $request->toArray());

        //Telegram::getWebhookUpdates();


        if (array_key_exists('callback_query', $arRequest)) {
            $strData = $arRequest['callback_query']['data'];
            Log::info('data', [$strData]);

            BotMessage::insert([
                'user_id' => $arRequest['callback_query']['from']['id'],
                'data'    => $strData
            ]);

            $chatId = $arRequest['callback_query']['message']['chat']['id'];
            Log::info('chat id', [$chatId]);

            if (str_contains($strData, 'project')) {
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

                $this->bot->sendMessage([
                    'chat_id'      => $chatId,
                    'text'         => 'Выберите утилиту:',
                    'reply_markup' => $keyboard
                ]);
            } elseif (str_contains($strData, 'utility')) {
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

                $this->bot->sendMessage([
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

                $this->bot->sendMessage([
                    'chat_id'      => $chatId,
                    'text'         => 'Выберите проект:',
                    'reply_markup' => $keyboard
                ]);
            } elseif (str_contains($strData, 'startAudit')) {
                $this->bot->sendMessage([
                    'chat_id'      => $chatId,
                    'text'         => 'Аудит проектов запущен. Ожидайте ссылки на отчеты',
                ]);
            }


        } else {
            $chatId = $arRequest['message']['chat']['id'];
            Log::info('no callback_query', ['chat_id' => $chatId]);

        }

        //$update = $this->botsManager->bot('max_security_audit_bot')->getWebhookUpdate();
        //Log::info('$update', (array)$update->get('data'));

        $this->botsManager->bot('max_security_audit_bot')->commandsHandler(true);
        $response = Telegram::bot('max_security_audit_bot')->getMe();

        return \response($response);
    }
}
