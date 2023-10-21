<?php

use App\Http\Controllers\Admin\MainController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UtilityController;
use Illuminate\Support\Facades\Route;


Route::get('/', [MainController::class, 'index'])->name('admin.main');

Route::prefix('users')->resource('users', UserController::class);

Route::prefix('projects')->resource('projects', ProjectController::class);

Route::prefix('utilities')->resource('utilities', UtilityController::class);
