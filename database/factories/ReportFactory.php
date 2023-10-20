<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Utility;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Report>
 */
class ReportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'status'     => fake()->text(10),
            'content'    => fake()->realText(),
            'utility_id' => Utility::factory(),
            'project_id' => Project::factory()
        ];
    }
}
