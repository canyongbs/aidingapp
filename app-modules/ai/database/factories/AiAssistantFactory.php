<?php

namespace AidingApp\Ai\Database\Factories;

use AidingApp\Ai\Enums\AiAssistantApplication;
use AidingApp\Ai\Enums\AiModel;
use AidingApp\Ai\Models\AiAssistant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AiAssistant>
 */
class AiAssistantFactory extends Factory
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
            'application' => AiAssistantApplication::Copilot,
            'model' => $this->faker->randomElement(AiModel::cases()),
            'created_by_id' => User::factory(),
        ];
    }
}
