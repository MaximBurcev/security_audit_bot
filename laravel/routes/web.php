<?php

use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\QuestionController;
use App\Http\Controllers\Dashboard\QuestionCategoryController;
use App\Http\Controllers\Dashboard\RoleController;
use App\Http\Controllers\Dashboard\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

App::setLocale('ru');

Route::get('/','App\Http\Controllers\CommonController@index')->name('homepage');

Route::group([
    'prefix' => '/dashboard',
    'as' => 'dashboard.',
    'middleware' => ['auth:sanctum', 'verified']
], static function (){
    Route::resource('question', QuestionController::class)->middleware('can:admin');
    Route::resource('role', RoleController::class)->middleware('can:admin');
    Route::resource('category', QuestionCategoryController::class)->middleware('can:admin');
    Route::resource('user', UserController::class)->middleware('can:admin');
    Route::post('question/addEmptyAnswer/{question}', [QuestionController::class, 'addEmptyAnswer'])->name('question.addEmptyAnswer')->middleware('can:admin');
    Route::get('/', [DashboardController::class,'index'])->name('index')->middleware('can:admin');
});
