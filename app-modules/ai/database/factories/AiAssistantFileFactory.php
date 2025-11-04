<?php

namespace AidingApp\Ai\Database\Factories;

use AidingApp\Ai\Models\AiAssistantFile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AiAssistantFile>
 */
class AiAssistantFileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'parsing_results' => $this->faker->text(),
        ];
    }
}
