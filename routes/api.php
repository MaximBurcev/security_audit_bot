<?php

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\V1\ReportController;
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

Route::middleware('auth:api')->get('/test', function (Request $request) {
    return 'Authenticated!';
});

function apiRoutesV1(Router $router)
{
    $router->apiResource('/reports', ReportController::class);
}

function apiRoutesV2(Router $router)
{
    $router->apiResource('/reports', \App\Http\Controllers\Api\V2\ReportController::class);
}

Route::group(['prefix' => 'v1', 'middleware' => 'auth:api'], apiRoutesV1(...));

Route::group(['prefix' => 'v2', 'middleware' => 'auth:api'], apiRoutesV2(...));
