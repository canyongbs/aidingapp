<?php

namespace AidingApp\Ai\Database\Factories;

use AidingApp\Ai\Models\AiMessage;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AiMessage>
 */
class AiMessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'message_id' => $this->faker->uuid(),
            'content' => $this->faker->sentence(),
            'context' => $this->faker->word(),
            'request' => $this->faker->word(),
            'user_id' => User::factory(),
        ];
    }
}
