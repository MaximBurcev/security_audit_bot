<?php

namespace App\Http\Controllers;

use App\Enums\ReportStatusEnum;
use App\Http\Controllers\Controller;
use App\Jobs\BotReportJob;
use App\Models\Audit;
use App\Models\BotMessage;
use App\Models\Project;
use App\Models\Report;
use App\Models\User;
use App\Models\Utility;
use App\Service\BotService;
use App\Service\ReportService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;
use Telegram\Bot\BotsManager;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;

class WebhookController extends Controller
{
    protected BotsManager $botsManager;
    private Api $bot;

    public function __construct(BotsManager $botsManager)
    {
        $this->bot = $botsManager->bot('max_security_audit_bot');
    }

    public function __invoke(Request $request): Response
    {

        BotService::handle($request, $this->bot);

        $this->bot->commandsHandler(true);
        $response = $this->bot->getMe();

        return \response($response);
    }
}
