<?php

namespace Database\Seeders;

use App\Models\QuestionCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @param QuestionCategory $model
     * @return void
     */
    public function run(QuestionCategory $model)
    {
        $tableName = $model->getTable();
        DB::table( $tableName )->delete();
        DB::statement("ALTER TABLE `{$tableName}` AUTO_INCREMENT = 1");

        DB::table( 'question_question_category' )->delete();
        DB::statement("ALTER TABLE `question_question_category` AUTO_INCREMENT = 1");


        for ($i = 0; $i < 50; $i++) {
            $exist = $model::factory()->create();

            for ($x = 0; $x < mt_rand(0,4); $x++) {
                DB::table('question_question_category')->insert(
                    ['question_id'=> mt_rand(1,50), 'question_category_id'=>$exist->id]
                );
            }
        }

    }
}
