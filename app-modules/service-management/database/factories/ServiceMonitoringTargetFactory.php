<?php

namespace AidingApp\ServiceManagement\Database\Factories;

use AidingApp\ServiceManagement\Enums\ServiceMonitoringFrequency;
use AidingApp\ServiceManagement\Models\ServiceMonitoringTarget;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ServiceMonitoringTarget>
 */
class ServiceMonitoringTargetFactory extends Factory
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
            'description' => fake()->paragraph(),
            'domain' => fake()->domainName(),
            'frequency' => fake()->randomElement(ServiceMonitoringFrequency::cases()),
        ];
    }
}
