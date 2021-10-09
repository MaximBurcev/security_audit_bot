<?php

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

Route::get('/', function () {
    return view('quiz.welcome', [
        'tasksGroups' => [
            'php' => '',
            'Laravel' => 'Laravel',
            'OOP' => 'OOP'
        ]
    ]);
});

Route::get('/task', function () {
    return view('quiz.task');
});

Route::resource('question', \App\Http\Controllers\QuestionController::class);

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
