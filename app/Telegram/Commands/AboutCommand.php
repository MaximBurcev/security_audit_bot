<?php

namespace App\Telegram\Commands;

use Illuminate\Support\Facades\Log;
use SergiX44\Nutgram\Nutgram;

class AboutCommand
{

    public function __construct(protected Nutgram $bot)
    {
    }

    public function handle(): void
    {
        Log::info('AboutCommand', [1]);


        $this->bot->sendMessage(
            'Сбор отчетов по безопасности проектов при помощи утилит:
https://www.kali.org/tools/nmap/
https://github.com/siberas/watobo
https://kali.tools/?p=2295
https://kali.tools/?p=816
https://www.kali.org/tools/sslscan/',
        );


    }
}
