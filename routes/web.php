<?php

use App\Http\Controllers\PublicReportController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::view('/', 'main')->name('main');

Route::view('/personal', 'personal')->name('personal')->middleware('auth');

Route::view('/terms', 'terms')->name('terms');

Auth::routes();

Route::post('/webhook', WebhookController::class);

Route::get('/public-report/{report}', [PublicReportController::class, 'show'])->name('public-report')->middleware('signed');

