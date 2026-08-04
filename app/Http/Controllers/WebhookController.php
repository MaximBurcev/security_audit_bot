<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use SergiX44\Nutgram\Nutgram;


class WebhookController extends Controller
{

    /**
     * Handle the telegram webhook request.
     */
    public function __invoke(Request $request, Nutgram $bot)
    {
        $this->assertFromTelegram($request);

        $bot->run();
    }

    /**
     * Проверяет секрет, который Telegram шлёт в заголовке X-Telegram-Bot-Api-Secret-Token
     * (задаётся при установке webhook). Без него личность отправителя берётся из тела
     * запроса, что позволяет подделать telegram_user_id и запускать аудиты от чужого имени.
     */
    private function assertFromTelegram(Request $request): void
    {
        $secret = config('nutgram.webhook_secret');

        if (empty($secret)) {
            Log::warning('Telegram webhook secret is not configured, request authenticity is not verified');
            return;
        }

        $received = (string) $request->header('X-Telegram-Bot-Api-Secret-Token', '');

        if (!hash_equals($secret, $received)) {
            abort(403);
        }
    }
}
