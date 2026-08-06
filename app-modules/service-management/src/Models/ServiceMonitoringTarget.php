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
use AidingApp\ServiceManagement\Database\Factories\ServiceMonitoringTargetFactory;
use AidingApp\ServiceManagement\Enums\ServiceMonitoringFrequency;
use AidingApp\ServiceManagement\Enums\ServiceMonitoringReportFrequency;
use AidingApp\ServiceManagement\Models\Scopes\ServiceMonitoringTargetVisibilityScope;
use AidingApp\ServiceManagement\Observers\ServiceMonitoringTargetObserver;
use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @mixin IdeHelperServiceMonitoringTarget
 */
#[ObservedBy([ServiceMonitoringTargetObserver::class])]
#[ScopedBy(ServiceMonitoringTargetVisibilityScope::class)]
class ServiceMonitoringTarget extends BaseModel implements Auditable
{
    /** @use HasFactory<ServiceMonitoringTargetFactory> */
    use HasFactory;

    use AuditableTrait;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'domain',
        'frequency',
        'is_notified_via_database',
        'is_notified_via_email',
        'is_reporting_active',
        'report_frequency',
        'is_reported_via_database',
        'is_reported_via_email',
        'is_confidential',
    ];

    protected $casts = [
        'frequency' => ServiceMonitoringFrequency::class,
        'is_reporting_active' => 'boolean',
        'report_frequency' => ServiceMonitoringReportFrequency::class,
        'is_reported_via_database' => 'boolean',
        'is_reported_via_email' => 'boolean',
    ];

    /**
     * @return HasMany<HistoricalServiceMonitoring, $this>
     */
    public function histories(): HasMany
    {
        return $this->hasMany(HistoricalServiceMonitoring::class);
    }

    /**
     * @return HasOne<HistoricalServiceMonitoring, $this>
     */
    public function latestHistory(): HasOne
    {
        return $this->hasOne(HistoricalServiceMonitoring::class)->latestOfMany();
    }

    /**
     * @return BelongsToMany<Department, $this, covariant ServiceMonitoringTargetDepartment>
     */
    public function departments(): BelongsToMany
    {
        return $this->belongsToMany(
            Department::class,
            'service_monitoring_target_department',
            'service_monitoring_target_id',
            'department_id',
        )
            ->using(ServiceMonitoringTargetDepartment::class)
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany<User, $this, covariant ServiceMonitoringTargetUser>
     */
    public function users(): BelongsToMany
    {
        return $this
            ->belongsToMany(User::class)
            ->using(ServiceMonitoringTargetUser::class)
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany<User, $this, covariant ServiceMonitoringTargetReportUser>
     */
    public function reportUsers(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'service_monitoring_target_report_user',
            'service_monitoring_target_id',
            'user_id',
        )
            ->using(ServiceMonitoringTargetReportUser::class)
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany<Department, $this, covariant ServiceMonitoringTargetReportDepartment>
     */
    public function reportDepartments(): BelongsToMany
    {
        return $this->belongsToMany(
            Department::class,
            'service_monitoring_target_report_department',
            'service_monitoring_target_id',
            'department_id',
        )
            ->using(ServiceMonitoringTargetReportDepartment::class)
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany<Contact, $this, covariant ServiceMonitoringTargetReportContact>
     */
    public function reportContacts(): BelongsToMany
    {
        return $this->belongsToMany(
            Contact::class,
            'service_monitoring_target_report_contact',
            'service_monitoring_target_id',
            'contact_id',
        )
            ->using(ServiceMonitoringTargetReportContact::class)
            ->withTimestamps();
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function createdBy(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return BelongsToMany<User, $this, covariant ServiceMonitoringTargetConfidentialUser>
     */
    public function confidentialUsers(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'service_monitoring_target_confidential_user',
            'service_monitoring_target_id',
            'user_id',
        )
            ->using(ServiceMonitoringTargetConfidentialUser::class)
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany<Department, $this, covariant ServiceMonitoringTargetConfidentialDepartment>
     */
    public function confidentialDepartments(): BelongsToMany
    {
        return $this->belongsToMany(
            Department::class,
            'service_monitoring_target_confidential_department',
            'service_monitoring_target_id',
            'department_id',
        )
            ->using(ServiceMonitoringTargetConfidentialDepartment::class)
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany<Contact, $this, covariant ServiceMonitoringTargetConfidentialContact>
     */
    public function confidentialContacts(): BelongsToMany
    {
        return $this->belongsToMany(
            Contact::class,
            'service_monitoring_target_confidential_contact',
            'service_monitoring_target_id',
            'contact_id',
        )
            ->using(ServiceMonitoringTargetConfidentialContact::class)
            ->withTimestamps();
    }

    public function getUptimePercentage(int $days): string
    {
        $serviceChecks = $this->histories()->where('created_at', '>=', now()->subDays($days))->orderBy('created_at')->get();

        if (now()->subDays($days)->diffInDays($serviceChecks->first()?->created_at) > 1) {
            return 'N/A';
        }

        $successes = $serviceChecks->where('succeeded', true);

        $percentage = ($successes->count() / $serviceChecks->count()) * 100;

        return ((int) $percentage === $percentage ? (int) $percentage : round($percentage, 1)) . '%';
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'frequency' => ServiceMonitoringFrequency::class,
            'is_notified_via_database' => 'boolean',
            'is_notified_via_email' => 'boolean',
            'is_confidential' => 'boolean',
        ];
    }
}
