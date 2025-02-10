<?php

namespace AidingApp\KnowledgeBase\Database\Factories;

use AidingApp\KnowledgeBase\Models\IncidentSeverity;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<IncidentSeverity>
 */
class IncidentSeverityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
        ];
    }
}
