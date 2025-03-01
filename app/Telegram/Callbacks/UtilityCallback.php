<?php

namespace App\Telegram\Callbacks;

use App\Services\BotMessageService;
use App\Services\UserService;
use App\Services\UtilityService;
use Illuminate\Support\Facades\Log;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class UtilityCallback
{

    public function __construct(
        protected Nutgram           $bot,
        protected int               $utilityId,
        protected BotMessageService $botMessageService,
        protected UserService       $userService
    )
    {
    }

    public function handle(): void
    {
        $strData = str_replace('{utilityId}', $this->utilityId, 'utility:{utilityId}');

        Log::info('$strData', [$strData]);


        $this->botMessageService->create([
            'user_id' => $this->userService->getByTelegramId($this->bot->userId())->id,
            'data'    => $strData
        ]);

        $inlineKeyboardMarkup = InlineKeyboardMarkup::make()->addRow(
            InlineKeyboardButton::make('Выбрать еще', callback_data: 'more'),
            InlineKeyboardButton::make('Начать аудит', callback_data: 'startAudit')
        );

        $this->bot->sendMessage(
            text: 'Что дальше?',
            reply_markup: $inlineKeyboardMarkup
        );
    }

}
