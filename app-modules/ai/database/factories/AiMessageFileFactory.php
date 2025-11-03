<?php

namespace AidingApp\Ai\Database\Factories;

use AidingApp\Ai\Models\AiMessage;
use AidingApp\Ai\Models\AiMessageFile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AiMessageFile>
 */
class AiMessageFileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'message_id' => AiMessage::factory(),
            'file_id' => $this->faker->uuid(),
            'name' => $this->faker->word(),
            'temporary_url' => $this->faker->url(),
            'mime_type' => $this->faker->mimeType(),
            'parsing_results' => $this->faker->text(),
        ];
    }
}