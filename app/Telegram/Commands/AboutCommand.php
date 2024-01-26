<?php

namespace App\Telegram\Commands;

use Illuminate\Support\Facades\Log;
use Telegram\Bot\Commands\Command;

class AboutCommand extends Command
{

    protected string $name = 'about';
    protected string $description = 'Информация по боту';
    public function handle()
    {
        Log::info('AboutCommand', [1]);
        $this->replyWithMessage([
            'text' => 'Сбор отчетов по безопасности проектов при помощи утилит:
https://www.kali.org/tools/nmap/
https://github.com/siberas/watobo
https://kali.tools/?p=2295
https://kali.tools/?p=816
https://www.kali.org/tools/sslscan/',
        ]);
    }
}
