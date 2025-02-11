<?php

namespace AidingApp\ServiceManagement\Database\Factories;

use AidingApp\ServiceManagement\Models\Incident;
use AidingApp\ServiceManagement\Models\IncidentSeverity;
use AidingApp\ServiceManagement\Models\IncidentStatus;
use AidingApp\Team\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Incident>
 */
class IncidentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->word(20),
            'description' => fake()->paragraph(),
            'severity_id' => IncidentSeverity::factory(),
            'status_id' => IncidentStatus::factory(),
            'assigned_team_id' => Team::factory(),
        ];
    }
}
