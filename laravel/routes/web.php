<?php

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

Route::get('/','App\Http\Controllers\CommonController@index');

// @todo Почистить это все
/*Route::group([
    'prefix' => '/question',
    'as' => 'question.',
], static function (){
    Route::get('/','App\Http\Controllers\QuestionController@index')->name('index');
    Route::get('/show/{question}','App\Http\Controllers\QuestionController@show')->name('show');
    Route::fallback('App\Http\Controllers\QuestionController@index');
});*/


Route::get('/task', function () {
    return view('quiz.task');
});

Route::group([
    'prefix' => '/dashboard',
    'as' => 'dashboard.',
], static function (){
    Route::resource('question', QuestionController::class);
    Route::resource('role', RoleController::class);
    Route::resource('category', QuestionCategoryController::class);
    Route::resource('user', UserController::class);
    Route::get('/','App\Http\Controllers\Dashboard\DashboardController@index');
});


Route::get('/user/1', function () {
    return view('user.view', [
        'user' => [
            'name' => 'Ivan Ivanov',
            'rating' => [
                'php' => 4,
                'Laravel' => 5,
                'OOP' => 3
            ]
        ]
    ]);
});
