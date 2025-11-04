<?php

namespace AidingApp\Ai\Database\Factories;

use AidingApp\Ai\Models\Prompt;
use AidingApp\Ai\Models\PromptType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Prompt>
 */
class PromptFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => str($this->faker->unique()->words(asText: true))->ucfirst()->toString(),
            'description' => $this->faker->optional()->sentences(asText: true),
            'prompt' => $this->faker->sentences(asText: true),
            'type_id' => PromptType::query()->inRandomOrder()->first() ?? PromptType::factory()->create(),
        ];
    }
}
