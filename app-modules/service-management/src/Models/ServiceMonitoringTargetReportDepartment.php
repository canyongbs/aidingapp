<?php

namespace AidingApp\ServiceManagement\Models;

use AidingApp\Department\Models\Department;
use AidingApp\ServiceManagement\Database\Factories\ServiceMonitoringTargetReportDepartmentFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @mixin IdeHelperServiceMonitoringTargetReportDepartment
 */
class ServiceMonitoringTargetReportDepartment extends Pivot
{
    use HasUuids;

    /** @use HasFactory<ServiceMonitoringTargetReportDepartmentFactory> */
    use HasFactory;

    public function getTable(): string
    {
        return 'service_monitoring_target_report_department';
    }

    /**
     * @return BelongsTo<ServiceMonitoringTarget, $this>
     */
    public function serviceMonitoringTarget(): BelongsTo
    {
        return $this->belongsTo(ServiceMonitoringTarget::class);
    }

    /**
     * @return BelongsTo<Department, $this>
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
}
