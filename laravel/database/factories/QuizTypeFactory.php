<?php

namespace Database\Factories;

use App\Models\QuizType;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuizTypeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = QuizType::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'console' => random_int(0,1) === 0 ? QuizType::CONSOLE_NO : QuizType::CONSOLE_YES,
            'ios' => random_int(0,1) === 0 ? QuizType::IOS_NO : QuizType::IOS_YES,
            'android' => random_int(0,1) === 0 ? QuizType::ANDROID_NO : QuizType::ANDROID_YES,
            'web' => random_int(0,1) === 0 ? QuizType::WEB_NO : QuizType::WEB_YES,
            'answers_types' => QuizType::ANSWERS_TYPES_BASIC,
        ];
    }
}
