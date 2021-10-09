<?php

namespace Database\Seeders;

use App\Models\Answer;
use Illuminate\Database\Seeder;

class AnswerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @param Answer $model
     * @return void
     */
    public function run(Answer $model)
    {
        $model::truncate();
        $model::factory(50)->create();
    }
}
