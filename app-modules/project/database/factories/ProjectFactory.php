<?php

namespace AidingApp\Project\Database\Factories;

use AidingApp\Project\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => str($this->faker->unique()->words(asText: 3))->title()->toString(),
            'description' => $this->faker->sentence(),
        ];
    }
}
