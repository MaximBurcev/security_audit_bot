<?php

namespace Database\Factories;

use App\Models\Answer;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnswerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Answer::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            //'text_en' => $this->faker->sentence,
            //'text_ru' => $this->faker->sentence,
            'right' => random_int(0,1) === 1 ? $this->model::RIGHT_YES : $this->model::RIGHT_NO,
            'question_id' => random_int(1,50)
        ];
    }
}
