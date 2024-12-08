<?php
/** @var SergiX44\Nutgram\Nutgram $bot */

use App\Models\Project;
use App\Services\ProjectService;
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

$bot->onCallbackQueryData('project:{projectId}', function(Nutgram $bot, $projectId){
    $bot->sendMessage(
        text: "You selected  #$projectId"
    );
});
