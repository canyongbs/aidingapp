<?php

namespace AidingApp\KnowledgeBase\Database\Factories;

use AidingApp\KnowledgeBase\Enums\SystemIncidentStatusClassification;
use AidingApp\KnowledgeBase\Models\IncidentStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<IncidentStatus>
 */
class IncidentStatusFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'classification' => fake()->randomElement(SystemIncidentStatusClassification::cases()),
            'name' => fake()->name(),
        ];
    }
}
