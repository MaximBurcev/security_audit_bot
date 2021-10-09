<?php

namespace Database\Factories;

use App\Models\Answer;
use App\Models\Question;
use App\Models\QuestionCategory;
use App\Models\Translation;
use Illuminate\Database\Eloquent\Factories\Factory;

class TranslationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Translation::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        // @todo It works fine, but I don't like it.
        $entities = [
            Question::class,
            Answer::class,
            QuestionCategory::class];

        $keys = [
            Question::class => [
                'text',
                'comment',
            ],
            Answer::class => [
                'text'
            ],
            QuestionCategory::class => [
                'title',
            ]
        ];
        $entity_type = $entities[random_int(0,count($entities)-1)];
        $keys = $keys[$entity_type];
        $key = $keys[random_int(0,count($keys)-1)];

        return [
            'entity_type' => $entity_type,
            'entity_id' => random_int(1,5),
            'locale' => random_int(0,1) === 0 ? 'ru' : 'en',
            'key' => $key,
            'value' => $this->faker->sentence,
        ];
    }
}
