<?php

use App\Http\Controllers\CommonController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\QuestionController;
use App\Http\Controllers\Dashboard\QuestionCategoryController;
use App\Http\Controllers\Dashboard\RoleController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\QuizController;
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

Route::get('/',[CommonController::class,'index'])->name('homepage');
Route::get('/{locale}/about',[CommonController::class,'about'])->name('about');
Route::get('/{locale}/pricing',[CommonController::class,'about'])->name('pricing');
Route::get('/{locale}/quiz',[QuizController::class,'index'])->name('quiz');
Route::post('/{locale}/quiz/run',[QuizController::class,'run'])->name('quiz.run');

Route::group([
    'prefix' => '/{locale}/dashboard',
    'as' => 'dashboard.',
    'middleware' => ['auth:sanctum', 'verified', 'can:admin']
], static function (){
    Route::resource('question', QuestionController::class);
    Route::resource('role', RoleController::class);
    Route::resource('category', QuestionCategoryController::class);
    Route::resource('user', UserController::class);
    Route::post('question/addEmptyAnswer/{question}', [QuestionController::class, 'addEmptyAnswer'])->name('question.addEmptyAnswer');
    Route::get('/', [DashboardController::class,'index'])->name('index');
});

/*Route::get('/ss',function (){
    User::factory()->create();
});*/
