<?php

namespace AidingApp\ServiceManagement\Database\Factories;

use AidingApp\Contact\Models\Contact;
use AidingApp\ServiceManagement\Models\ServiceMonitoringTarget;
use AidingApp\ServiceManagement\Models\ServiceMonitoringTargetReportContact;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ServiceMonitoringTargetReportContact>
 */
class ServiceMonitoringTargetReportContactFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'service_monitoring_target_id' => ServiceMonitoringTarget::factory(),
            'contact_id' => Contact::factory(),
        ];
    }
}
