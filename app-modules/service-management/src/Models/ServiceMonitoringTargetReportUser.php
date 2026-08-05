<?php

namespace AidingApp\ServiceManagement\Models;

use AidingApp\ServiceManagement\Database\Factories\ServiceMonitoringTargetReportUserFactory;
use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @mixin IdeHelperServiceMonitoringTargetReportUser
 */
class ServiceMonitoringTargetReportUser extends Pivot
{
    use HasUuids;

    /** @use HasFactory<ServiceMonitoringTargetReportUserFactory> */
    use HasFactory;

    public function getTable(): string
    {
        return 'service_monitoring_target_report_user';
    }

    /**
     * @return BelongsTo<ServiceMonitoringTarget, $this>
     */
    public function serviceMonitoringTarget(): BelongsTo
    {
        return $this->belongsTo(ServiceMonitoringTarget::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
