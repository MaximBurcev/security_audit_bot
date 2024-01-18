<?php

namespace App\Telegram\Commands;

use Telegram\Bot\Commands\Command;

class StartCommand extends Command
{

    protected string $name = 'start';
    protected string $description = 'Запуск/Перезапуск бота';
    public function handle()
    {
        // TODO: Implement handle() method.
    }
}
