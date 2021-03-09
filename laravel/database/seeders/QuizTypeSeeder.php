<?php

namespace Database\Seeders;

use App\Models\QuizType;
use Illuminate\Database\Seeder;

class QuizTypeSeeder extends Seeder
{

    public function run(QuizType $model)
    {
        $model::factory(5)->create();
    }
}
