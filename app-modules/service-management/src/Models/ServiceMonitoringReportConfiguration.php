<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Aiding App® is licensed under the Elastic License 2.0. For more details,
    see <https://github.com/canyongbs/aidingapp/blob/main/LICENSE.>

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Aiding App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AidingApp\ServiceManagement\Models;

use AidingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use AidingApp\Contact\Models\Contact;
use AidingApp\Department\Models\Department;
use AidingApp\ServiceManagement\Database\Factories\ServiceMonitoringReportConfigurationFactory;
use AidingApp\ServiceManagement\Enums\ServiceMonitoringReportFrequency;
use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @mixin IdeHelperServiceMonitoringReportConfiguration
 */
class ServiceMonitoringReportConfiguration extends BaseModel implements Auditable
{
    /** @use HasFactory<ServiceMonitoringReportConfigurationFactory> */
    use HasFactory;

    use AuditableTrait;

    protected $fillable = [
        'service_monitoring_target_id',
        'frequency',
        'is_active',
        'is_reported_via_email',
        'is_reported_via_database',
    ];

    protected $casts = [
        'frequency' => ServiceMonitoringReportFrequency::class,
        'is_active' => 'boolean',
        'is_reported_via_email' => 'boolean',
        'is_reported_via_database' => 'boolean',
    ];

    /**
     * @return BelongsTo<ServiceMonitoringTarget, $this>
     */
    public function serviceMonitoringTarget(): BelongsTo
    {
        return $this->belongsTo(ServiceMonitoringTarget::class);
    }

    /**
     * @return BelongsToMany<User, $this, covariant ServiceMonitoringReportConfigurationUser>
     */
    public function reportUsers(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'service_monitoring_report_configuration_user',
            'service_monitoring_report_configuration_id',
            'user_id',
        )
            ->using(ServiceMonitoringReportConfigurationUser::class)
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany<Department, $this, covariant ServiceMonitoringReportConfigurationDepartment>
     */
    public function reportDepartments(): BelongsToMany
    {
        return $this->belongsToMany(
            Department::class,
            'service_monitoring_report_configuration_department',
            'service_monitoring_report_configuration_id',
            'department_id',
        )
            ->using(ServiceMonitoringReportConfigurationDepartment::class)
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany<Contact, $this, covariant ServiceMonitoringReportConfigurationContact>
     */
    public function reportContacts(): BelongsToMany
    {
        return $this->belongsToMany(
            Contact::class,
            'service_monitoring_report_configuration_contact',
            'service_monitoring_report_configuration_id',
            'contact_id',
        )
            ->using(ServiceMonitoringReportConfigurationContact::class)
            ->withTimestamps();
    }
}
