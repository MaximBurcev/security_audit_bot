<?php

use App\Models\QuizType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuizTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quiz_types', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('console')->default(QuizType::CONSOLE_NO);
            $table->unsignedTinyInteger('ios')->default(QuizType::IOS_YES);
            $table->unsignedTinyInteger('android')->default(QuizType::ANDROID_YES);
            $table->unsignedTinyInteger('web')->default(QuizType::WEB_NO);
            $table->unsignedTinyInteger('answers_types')->default(QuizType::ANSWERS_TYPES_BASIC);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quiz_types');
    }
}
