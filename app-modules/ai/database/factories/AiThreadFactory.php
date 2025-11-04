<?php

namespace AidingApp\Ai\Database\Factories;

use AidingApp\Ai\Models\AiAssistant;
use AidingApp\Ai\Models\AiThread;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AiThread>
 */
class AiThreadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'thread_id' => $this->faker->uuid(),
            'name' => $this->faker->word(),
            'assistant_id' => AiAssistant::factory(),
            'user_id' => User::factory(),
        ];
    }

    public function saved(): AiThreadFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => $this->faker->word(),
                'saved_at' => $this->faker->dateTime(),
            ];
        });
    }
}
