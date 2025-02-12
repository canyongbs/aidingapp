<?php

namespace AidingApp\ServiceManagement\Tests\RequestFactories;

use Worksome\RequestFactories\RequestFactory;

class IncidentSeverityRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'name' => fake()->word(10),
        ];
    }
}
