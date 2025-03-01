<?php
/** @var SergiX44\Nutgram\Nutgram $bot */

use App\Services\AuditService;
use App\Services\BotMessageService;
use App\Services\ProjectService;
use App\Services\ReportService;
use App\Services\UserService;
use App\Services\UtilityService;
use App\Telegram\Callbacks\ProjectCallback;
use App\Telegram\Callbacks\UtilityCallback;
use App\Telegram\Commands\AboutCommand;
use App\Telegram\Commands\StartAuditCommand;
use App\Telegram\Commands\StartCommand;
use SergiX44\Nutgram\Nutgram;

/*
|--------------------------------------------------------------------------
| Nutgram Handlers
|--------------------------------------------------------------------------
|
| Here is where you can register telegram handlers for Nutgram. These
| handlers are loaded by the NutgramServiceProvider. Enjoy!
|
*/


$bot->onCommand('start', function (Nutgram $bot, ProjectService $projectService, UserService $userService) {

    (new StartCommand($bot, $projectService, $userService))->handle();

});

$bot->onCallbackQueryData('project:{projectId}', function (Nutgram $bot, $projectId, BotMessageService $botMessageService, UtilityService $utilityService, UserService $userService) {

    (new ProjectCallback($bot, $projectId, $botMessageService, $utilityService, $userService))->handle();
});

$bot->onCallbackQueryData('utility:{utilityId}', function (Nutgram $bot, $utilityId, BotMessageService $botMessageService, UserService $userService) {

    (new UtilityCallback($bot, $utilityId, $botMessageService, $userService))->handle();
});

$bot->onCallbackQueryData('more', function (Nutgram $bot, ProjectService $projectService, UserService $userService) {
    (new StartCommand($bot, $projectService, $userService))->handle();
});

$bot->onCallbackQueryData('startAudit', function (Nutgram $bot, ProjectService $projectService, BotMessageService $botMessageService, ReportService $reportService, AuditService $auditService, UserService $userService, UtilityService $utilityService) {
    (new StartAuditCommand($bot, $projectService, $botMessageService, $reportService, $auditService, $userService, $utilityService))->handle();
});

$bot->onCommand('about', function (Nutgram $bot) {
    (new AboutCommand($bot))->handle();
});


