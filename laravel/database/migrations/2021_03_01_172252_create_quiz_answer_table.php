<?php

use App\Models\Answer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuizAnswerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quiz_answer', function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId('quiz_id')
                ->constrained('quizzes')
                ->onDelete('cascade');
            $table
                ->foreignId('question_id')
                ->constrained('questions')
                ->onDelete('cascade');
            $table
                ->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');
            $table
                ->foreignId('answer_id')
                ->nullable()
                ->constrained('answers')
                ->onDelete('cascade');
            $table->unsignedTinyInteger('right')->default(Answer::RIGHT_NO);
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
        Schema::dropIfExists('quiz_answer');
    }
}
