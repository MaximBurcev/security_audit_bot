<?php

namespace App\Telegram\Commands;

use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;

class StartCommand extends Command
{


    protected string $name = 'start';
    protected string $description = 'Запуск/Перезапуск бота';
    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function handle()
    {
        Log::info('StartCommand', [1]);
        $userData = $this->getUpdate()->message->from;
        Log::info('$userData id', [$userData->id]);
        $userId = $userData->id;
        $telegramUser = $this->user->where('telegram_user_id', '=', $userId)->first();
        if (!$telegramUser) {
            $this->addNewTelegramUser($userData);
            $this->replyWithMessage(['text' => 'Добро пожаловать в наш телеграм бот!🥳']);
        } else {
            $this->replyWithMessage(['text' => 'Рады видеть вас снова!🥳']);
        }

        $arProject = [];
        foreach(Project::all() as $project) {
            $arProject[] = Keyboard::button(['text' => $project->title, 'callback_data' => 'project:' . $project->id]);
        }

        $keyboard = Keyboard::make()
            ->inline()
            ->row($arProject);

        $this->replyWithMessage([
            'text'         => 'Выберите проект:',
            'reply_markup' => $keyboard
        ]);
    }

    private function addNewTelegramUser($userData)
    {
        $this->user->insert([
            'name'              => $userData->username,
            'email'             => $userData->username . '@' . parse_url(config('app.url'), PHP_URL_HOST),
            'email_verified_at' => now(),
            'password'          => Hash::make(fake()->password()),
            'remember_token'    => Str::random(10),
            'telegram_user_id'  => $userData->id
        ]);
    }
}
