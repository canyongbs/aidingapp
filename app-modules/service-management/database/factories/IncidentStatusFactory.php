<?php

namespace AidingApp\ServiceManagement\Database\Factories;

use AidingApp\ServiceManagement\Enums\SystemIncidentStatusClassification;
use AidingApp\ServiceManagement\Models\IncidentStatus;
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
            'name' => fake()->word(10),
            'classification' => fake()->randomElement(SystemIncidentStatusClassification::cases()),
        ];
    }
}
