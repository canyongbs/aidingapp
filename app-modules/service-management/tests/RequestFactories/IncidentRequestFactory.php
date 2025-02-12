<?php

namespace AidingApp\ServiceManagement\Tests\RequestFactories;

use AidingApp\ServiceManagement\Models\IncidentSeverity;
use AidingApp\ServiceManagement\Models\IncidentStatus;
use AidingApp\Team\Models\Team;
use Worksome\RequestFactories\RequestFactory;

class IncidentRequestFactory extends RequestFactory
{
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
