<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\Translation;
use Faker\Factory as FakerFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @param Question $model
     * @return void
     */
    public function run(Question $model)
    {
        $tableName = $model->getTable();
        DB::table( $tableName )->delete();
        DB::statement("ALTER TABLE `{$tableName}` AUTO_INCREMENT = 1");

        // @todo It doesn't works. Why?
        $model::factory()->hasTranslations(3)->create();


        // @todo So let's try another option
        $faker = FakerFactory::create();
        for ($x = 0; $x < 50; $x++) {
            $exist = $model::factory()->create();
            $translations = [
                new Translation(
                    [
                        'locale'=>'ru','key'=>'title','value'=> $faker->sentence,
                        'entity_type' => Question::class,
                        'entity_id' => $exist->isDirty()
                    ])
            ];

            //@todo It works, but Translation will not save too
            $exist->translations()->saveMany($translations);
        }
    }
}
