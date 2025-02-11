<?php

namespace AidingApp\ServiceManagement\Database\Factories;

use AidingApp\ServiceManagement\Enums\IncidentSeverityColorOptions;
use AidingApp\ServiceManagement\Models\IncidentSeverity;
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
            'name' => fake()->word(10),
            'color' => $this->faker->randomElement(IncidentSeverityColorOptions::cases()),
        ];
    }
}
