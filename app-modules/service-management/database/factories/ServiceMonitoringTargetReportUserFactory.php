<?php

namespace AidingApp\ServiceManagement\Database\Factories;

use AidingApp\ServiceManagement\Models\ServiceMonitoringTarget;
use AidingApp\ServiceManagement\Models\ServiceMonitoringTargetReportUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ServiceMonitoringTargetReportUser>
 */
class ServiceMonitoringTargetReportUserFactory extends Factory
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
            'user_id' => User::factory(),
        ];
    }
}
