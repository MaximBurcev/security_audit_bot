<?php

namespace App\Telegram\Commands;

use App\Models\User;
use App\Services\ProjectService;
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

    public function __construct(protected Nutgram $bot, protected ProjectService $projectService)
    {

    }

    public function handle(): void
    {
        /*

        Log::info('StartCommand', [1]);
        $userData = $this->getUpdate()->message->from;
        Log::info('$userData', [$userData]);
        $userId = $userData->id;
        $telegramUser = $this->user->where('telegram_user_id', '=', $userId)->first();
        if (!$telegramUser) {
            $this->addNewTelegramUser($userData);
            $this->replyWithMessage(['text' => 'Добро пожаловать в наш телеграм бот!🥳']);
        } else {
            $this->replyWithMessage(['text' => 'Рады видеть вас снова!🥳']);
        }

        */

        Log::info('StartCommand', [1]);

        $inlineKeyboardMarkup = InlineKeyboardMarkup::make();

        foreach($this->projectService->getAll() as $project) {
            $inlineKeyboardMarkup->addRow(InlineKeyboardButton::make($project->title, callback_data: 'project:' . $project->id));
        }

        $this->bot->sendMessage(
            text: 'Выберите проект:',
            reply_markup: $inlineKeyboardMarkup
        );
    }

    private function addNewTelegramUser($userData): void
    {
        $this->user->insert([
            'name'              => $userData->username?? $userData->first_name,
            'email'             => $userData->username?? $userData->first_name . '@' . parse_url(config('app.url'), PHP_URL_HOST),
            'email_verified_at' => now(),
            'password'          => Hash::make(fake()->password()),
            'remember_token'    => Str::random(10),
            'telegram_user_id'  => $userData->id
        ]);
    }
}
