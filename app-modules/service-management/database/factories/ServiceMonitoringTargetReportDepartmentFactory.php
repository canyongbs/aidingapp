<?php

namespace AidingApp\ServiceManagement\Database\Factories;

use AidingApp\Department\Models\Department;
use AidingApp\ServiceManagement\Models\ServiceMonitoringTarget;
use AidingApp\ServiceManagement\Models\ServiceMonitoringTargetReportDepartment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ServiceMonitoringTargetReportDepartment>
 */
class ServiceMonitoringTargetReportDepartmentFactory extends Factory
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
            'department_id' => Department::factory(),
        ];
    }
}
