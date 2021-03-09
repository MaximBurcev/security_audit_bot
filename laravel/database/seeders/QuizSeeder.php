<?php

namespace Database\Seeders;

use App\Models\Answer;
use App\Models\Quiz;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuizSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Quiz $model)
    {

        for ($i = 0; $i < 10; $i++) {
            $exist = $model::factory()->create();

            for ($x = 0; $x < random_int(0,4); $x++) {
                DB::table('quiz_user')->insert(
                    ['user_id'=> random_int(1,5), 'quiz_id'=>$exist->id]
                );

                DB::table('question_quiz')->insert(
                    ['question_id'=> random_int(1,5), 'quiz_id'=>$exist->id]
                );

                DB::table('quiz_answer')->insert(
                    [
                        'quiz_id'=>$exist->id,
                        'question_id'=> random_int(1,5),
                        'user_id'=> random_int(1,5),
                        'answer_id'=> null,
                        'right' => Answer::RIGHT_YES
                    ]
                );
            }
        }

    }
}
