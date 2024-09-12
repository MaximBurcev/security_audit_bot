<?php

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\V1\ReportController;
use App\Http\Controllers\WebhookController;
use App\Models\Audit;
use App\Models\Project;
use App\Models\Report;
use App\Models\Task;
use App\Models\User;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::apiResource('/users', UserController::class)->middleware('auth:api');

Route::middleware('auth:api')->get('/statistic', function (Request $request) {
    return [
        'users'     => User::query()->count(),
        'reports'   => Report::query()->count(),
        'audits'    => Audit::query()->count(),
        'projects'  => Project::query()->count(),
        'tasks'     => Task::query()->count(),
        'utilities' => Utility::query()->count(),
    ];
});


