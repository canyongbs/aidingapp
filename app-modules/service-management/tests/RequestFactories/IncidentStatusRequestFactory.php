<?php

namespace AidingApp\ServiceManagement\Tests\RequestFactories;

use AidingApp\ServiceManagement\Enums\SystemIncidentStatusClassification;
use Worksome\RequestFactories\RequestFactory;

class IncidentStatusRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'name' => fake()->word(10),
            'classification' => fake()->randomElement(SystemIncidentStatusClassification::cases()),
        ];
    }
}
