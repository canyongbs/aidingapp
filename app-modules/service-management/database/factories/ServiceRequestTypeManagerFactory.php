<?php

namespace AidingApp\ServiceManagement\Database\Factories;

use AidingApp\Team\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeManager;

/**
 * @extends Factory<ServiceRequestTypeManager>
 */
class ServiceRequestTypeManagerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'service_request_type_id' => ServiceRequestType::factory(),
            'team_id' => Team::factory(),
        ];
    }
}
