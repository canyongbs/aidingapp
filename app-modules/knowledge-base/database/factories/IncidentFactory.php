<?php

namespace AidingApp\KnowledgeBase\Database\Factories;

use AidingApp\KnowledgeBase\Models\Incident;
use AidingApp\KnowledgeBase\Models\IncidentSeverity;
use AidingApp\KnowledgeBase\Models\IncidentStatus;
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
            'title' => fake()->sentence(),
            'description' => fake()->sentence(),
            'severity_id' => IncidentSeverity::inRandomOrder()->first() ?? IncidentSeverity::factory(),
            'status_id' => IncidentStatus::inRandomOrder()->first() ?? IncidentStatus::factory(),
            'assigned_team_id' => Team::factory(),
        ];
    }
}
