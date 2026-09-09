<?php

namespace AidingApp\ServiceManagement\Database\Factories;

use AidingApp\Group\Models\Group;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeAuditorGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ServiceRequestTypeAuditorGroup> */
class ServiceRequestTypeAuditorGroupFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'service_request_type_id' => ServiceRequestType::factory(),
            'group_id' => Group::factory(),
        ];
    }
}
