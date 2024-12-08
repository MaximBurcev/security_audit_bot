<?php

namespace App\Http\Controllers;
use SergiX44\Nutgram\Nutgram;


class WebhookController extends Controller
{

    /**
     * Handle the telegram webhook request.
     */
    public function __invoke(Nutgram $bot)
    {
        $bot->run();
    }
}
