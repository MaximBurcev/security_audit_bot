<?php

namespace App\Telegram\Commands;

use App\Models\User;
use App\Services\ProjectService;
use App\Services\UserService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class StartCommand
{


    protected string $name = 'start';
    protected string $description = 'Запуск/Перезапуск бота';
    private User $user;

    public function __construct(
        protected Nutgram $bot,
        protected ProjectService $projectService,
        protected UserService $userService
    )
    {

    }

    public function handle(): void
    {


        Log::info('StartCommand', [1]);
        $telegramUser = $this->bot->user();
        Log::info('$telegramUser', [$telegramUser]);
        $user = $this->userService->getByTelegramId($telegramUser->id);
        if (!$user) {
            $this->userService->addTelegramUser($telegramUser);
            $this->bot->sendMessage('Добро пожаловать в наш телеграм бот!🥳');
        } else {
            $this->bot->sendMessage('Рады видеть вас снова!🥳');
        }



        $inlineKeyboardMarkup = InlineKeyboardMarkup::make();

        foreach($this->projectService->getAll() as $project) {
            $inlineKeyboardMarkup->addRow(InlineKeyboardButton::make($project->title, callback_data: 'project:' . $project->id));
        }

        $this->bot->sendMessage(
            text: 'Выберите проект:',
            reply_markup: $inlineKeyboardMarkup
        );
    }


}
