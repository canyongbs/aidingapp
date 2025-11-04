<?php

namespace AidingApp\Ai\Database\Factories;

use AidingApp\Ai\Models\PromptType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PromptType>
 */
class PromptTypeFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => str($this->faker->unique()->words(asText: true))->ucfirst()->toString(),
            'description' => $this->faker->optional()->sentences(asText: true),
        ];
    }
}
