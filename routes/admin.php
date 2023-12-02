<?php

use App\Http\Controllers\Admin\AuditController;
use App\Http\Controllers\Admin\MainController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UtilityController;
use Illuminate\Support\Facades\Route;


Route::group(['middleware' => ['auth'], 'prefix' => 'admin'], function () {
    Route::prefix('projects')->resource('projects', ProjectController::class);

    Route::prefix('utilities')->resource('utilities', UtilityController::class);

    Route::prefix('reports')->resource('reports', ReportController::class);

    Route::prefix('audits')->resource('audits', AuditController::class);
});

Route::group(['middleware' => 'lang', 'prefix' => '{lang}/admin', 'where' => ['lang' => 'en|ru']], function () {
    Route::get('/', [MainController::class, 'index'])->name('admin.main');
});

Route::group(['middleware' => 'lang', 'prefix' => '{lang}/admin', 'where' => ['lang' => 'en|ru']], function () {
    Route::resource('users', UserController::class);
});



