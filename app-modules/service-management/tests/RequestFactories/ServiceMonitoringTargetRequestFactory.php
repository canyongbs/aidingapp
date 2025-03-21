<?php

namespace AidingApp\ServiceManagement\Tests\RequestFactories;

use AidingApp\ServiceManagement\Enums\ServiceMonitoringFrequency;
use Worksome\RequestFactories\RequestFactory;

class ServiceMonitoringTargetRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'name' => fake()->word(10),
            'description' => fake()->paragraph(),
            'domain' => fake()->url(),
            'frequency' => fake()->randomElement(ServiceMonitoringFrequency::cases()),
        ];
    }
}
