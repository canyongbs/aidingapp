<?php

namespace AidingApp\Project\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\AidingApp\Project\Models\ProjectMilestoneStatus>
 */
class ProjectMilestoneStatusFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(
                $this->faker->numberBetween(1, 4),
                true
            ),
            'description' => $this->faker->sentence(),
        ];
    }
}
