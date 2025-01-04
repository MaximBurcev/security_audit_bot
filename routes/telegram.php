<?php
/** @var SergiX44\Nutgram\Nutgram $bot */

use App\Services\AuditService;
use App\Services\BotMessageService;
use App\Services\ProjectService;
use App\Services\ReportService;
use App\Services\UserService;
use App\Services\UtilityService;
use App\Telegram\Commands\AboutCommand;
use App\Telegram\Commands\StartAuditCommand;
use App\Telegram\Commands\StartCommand;
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

    (new StartCommand($bot, $projectService))->handle();

});

$bot->onCallbackQueryData('project:{projectId}', function (Nutgram $bot, $projectId, BotMessageService $botMessageService, UtilityService $utilityService) {

    $userId = $bot->userId();
    $strData = str_replace('{projectId}', $projectId, 'project:{projectId}');

    Log::info('$userId', [$userId]);
    Log::info('$strData', [$strData]);


    $botMessageService->create([
        'user_id' => $userId,
        'data'    => $strData
    ]);

    $inlineKeyboardMarkup = InlineKeyboardMarkup::make();

    foreach ($utilityService->getAll() as $utility) {
        $inlineKeyboardMarkup->addRow(InlineKeyboardButton::make($utility->title, callback_data: 'utility:' . $utility->id));
    }

    $bot->sendMessage(
        text: 'Выберите утилиту:',
        reply_markup: $inlineKeyboardMarkup
    );
});

$bot->onCallbackQueryData('utility:{utilityId}', function (Nutgram $bot, $utilityId, BotMessageService $botMessageService, UtilityService $utilityService) {

    $userId = $bot->userId();
    $strData = str_replace('{utilityId}', $utilityId, 'utility:{utilityId}');

    Log::info('$userId', [$userId]);
    Log::info('$strData', [$strData]);


    $botMessageService->create([
        'user_id' => $userId,
        'data'    => $strData
    ]);

    $inlineKeyboardMarkup = InlineKeyboardMarkup::make()->addRow(
        InlineKeyboardButton::make('Выбрать еще', callback_data: 'more'),
        InlineKeyboardButton::make('Начать аудит', callback_data: 'startAudit')
    );

    $bot->sendMessage(
        text: 'Что дальше?',
        reply_markup: $inlineKeyboardMarkup
    );
});

$bot->onCallbackQueryData('more', function (Nutgram $bot, ProjectService $projectService) {
    (new StartCommand($bot, $projectService))->handle();
});

$bot->onCallbackQueryData('startAudit', function (Nutgram $bot, ProjectService $projectService, BotMessageService $botMessageService, ReportService $reportService, AuditService $auditService, UserService $userService, UtilityService $utilityService) {
    (new StartAuditCommand($bot, $projectService, $botMessageService, $reportService, $auditService, $userService, $utilityService))->handle();
});

$bot->onCommand('about', function (Nutgram $bot) {
    (new AboutCommand($bot))->handle();
});
