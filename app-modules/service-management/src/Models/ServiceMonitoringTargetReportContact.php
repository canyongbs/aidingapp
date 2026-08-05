<?php

namespace AidingApp\ServiceManagement\Models;

use AidingApp\Contact\Models\Contact;
use AidingApp\ServiceManagement\Database\Factories\ServiceMonitoringTargetReportContactFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @mixin IdeHelperServiceMonitoringTargetReportContact
 */
class ServiceMonitoringTargetReportContact extends Pivot
{
    use HasUuids;

    /** @use HasFactory<ServiceMonitoringTargetReportContactFactory> */
    use HasFactory;

    public function getTable(): string
    {
        return 'service_monitoring_target_report_contact';
    }

    /**
     * @return BelongsTo<ServiceMonitoringTarget, $this>
     */
    public function serviceMonitoringTarget(): BelongsTo
    {
        return $this->belongsTo(ServiceMonitoringTarget::class);
    }

    /**
     * @return BelongsTo<Contact, $this>
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }
}
