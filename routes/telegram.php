<?php
/** @var SergiX44\Nutgram\Nutgram $bot */

use App\Models\Project;
use App\Services\BotMessageService;
use App\Services\ProjectService;
use App\Services\UtilityService;
use Illuminate\Support\Facades\Log;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

/*
|--------------------------------------------------------------------------
| Nutgram Handlers
|--------------------------------------------------------------------------
|
| Here is where you can register telegram handlers for Nutgram. These
| handlers are loaded by the NutgramServiceProvider. Enjoy!
|
*/


$bot->onCommand('start', function (Nutgram $bot, ProjectService $projectService) {
    Log::info('StartCommand', [1]);

    $inlineKeyboardMarkup = InlineKeyboardMarkup::make();

    foreach($projectService->getAll() as $project) {
        $inlineKeyboardMarkup->addRow(InlineKeyboardButton::make($project->title, callback_data: 'project:' . $project->id));
    }

    $bot->sendMessage(
        text: 'Выберите проект:',
        reply_markup: $inlineKeyboardMarkup
    );

});

$bot->onCallbackQueryData('project:{projectId}', function(Nutgram $bot, $projectId, BotMessageService $botMessageService, UtilityService $utilityService) {

    $userId = $bot->userId();
    $strData = str_replace('{projectId}', $projectId, 'project:{projectId}');

    Log::info('$userId', [$userId]);
    Log::info('$strData', [$strData]);


    $botMessageService->create([
        'user_id' => $userId,
        'data'    => $strData
    ]);

    $inlineKeyboardMarkup = InlineKeyboardMarkup::make();

    foreach($utilityService->getAll() as $utility) {
        $inlineKeyboardMarkup->addRow(InlineKeyboardButton::make($utility->title, callback_data: 'utility:' . $utility->id));
    }

    $bot->sendMessage(
        text: 'Выберите утилиту:',
        reply_markup: $inlineKeyboardMarkup
    );
});

$bot->onCallbackQueryData('utility:{utilityId}', function(Nutgram $bot, $utilityId, BotMessageService $botMessageService, UtilityService $utilityService) {
   $bot->sendMessage($utilityId);
});

$bot->onCommand('about', function (Nutgram $bot) {
    (new \App\Telegram\Commands\AboutCommand($bot))->handle();
});
