<?php

namespace AidingApp\IntegrationOpenAi\Database\Factories;

use AidingApp\IntegrationOpenAi\Models\OpenAiVectorStore;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OpenAiVectorStore>
 */
class OpenAiVectorStoreFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'deployment_hash' => $this->faker->md5(),
            'ready_until' => $this->faker->dateTimeBetween('+1 day', '+1 year'),
            'vector_store_id' => $this->faker->uuid(),
            'vector_store_file_id' => $this->faker->uuid(),
        ];
    }
}