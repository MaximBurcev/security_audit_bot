<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;
use Telegram\Bot\BotsManager;

class WebhookController extends Controller
{
    protected BotsManager $botsManager;

   public function __construct(BotsManager $botsManager)
   {
       $this->botsManager = $botsManager;
   }

   public function __invoke(Request $request): Response
   {
       Log::info('request', $request->toArray());
       $this->botsManager->bot()->commandsHandler(true);
       return response(null, 200);
   }
}
