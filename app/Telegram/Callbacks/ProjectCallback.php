<?php

namespace App\Telegram\Callbacks;

use App\Services\BotMessageService;
use App\Services\UserService;
use App\Services\UtilityService;
use Illuminate\Support\Facades\Log;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class ProjectCallback
{
    public function __construct(
        protected Nutgram           $bot,
        protected int               $projectId,
        protected BotMessageService $botMessageService,
        protected UtilityService    $utilityService,
        protected UserService       $userService
    )
    {
    }

    public function handle(): void
    {
        $strData = str_replace('{projectId}', $this->projectId, 'project:{projectId}');

        Log::info('telegram user id', [$this->bot->userId()]);
        Log::info('$strData', [$strData]);


        $this->botMessageService->create([
            'user_id' => $this->userService->getByTelegramId($this->bot->userId())->id,
            'data'    => $strData
        ]);

        $inlineKeyboardMarkup = InlineKeyboardMarkup::make();

        foreach ($this->utilityService->getAll() as $utility) {
            $inlineKeyboardMarkup->addRow(InlineKeyboardButton::make($utility->title, callback_data: 'utility:' . $utility->id));
        }

        $this->bot->sendMessage(
            text: 'Выберите утилиту:',
            reply_markup: $inlineKeyboardMarkup
        );
    }
}
