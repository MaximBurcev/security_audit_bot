<?php

use Illuminate\Support\Facades\App;
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
App::setLocale('ru');

Route::get('/', function () {
    return view('main');
})->name('main');

Route::get('/personal', function () {
    return view('personal');
})->name('personal');

Route::get('/register', function () {
    return view('register');
})->name('register');

Route::get('/terms', function () {
    return view('terms');
})->name('terms');
