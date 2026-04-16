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
use AidingApp\ServiceManagement\Database\Factories\ServiceRequestTypeFactory;
use AidingApp\ServiceManagement\Enums\ServiceRequestCategory;
use AidingApp\ServiceManagement\Enums\ServiceRequestIssueCategory;
use AidingApp\ServiceManagement\Enums\ServiceRequestTypeAssignmentTypes;
use AidingApp\ServiceManagement\Observers\ServiceRequestTypeObserver;
use AidingApp\Team\Models\Team;
use App\Features\ServiceRequestCategoryRenameFeature;
use App\Models\BaseModel;
use App\Models\User;
use CanyonGBS\Common\Models\Concerns\CanBeArchived;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @property \AidingApp\ServiceManagement\Enums\ServiceRequestCategory|null $default_issue_category
 *
 * @mixin IdeHelperServiceRequestType
 */
#[ObservedBy([ServiceRequestTypeObserver::class])]
class ServiceRequestType extends BaseModel implements Auditable
{
    use CanBeArchived;
    use SoftDeletes;
    use HasUuids;
    use AuditableTrait;

    /** @use HasFactory<ServiceRequestTypeFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'has_enabled_feedback_collection',
        'has_enabled_csat',
        'has_enabled_nps',
        'description',
        'icon',
        'default_issue_category',
        'default_category',
        'assignment_type',
        'is_managers_service_request_created_email_enabled',
        'is_managers_service_request_created_notification_enabled',
        'is_managers_service_request_assigned_email_enabled',
        'is_managers_service_request_assigned_notification_enabled',
        'is_managers_service_request_closed_email_enabled',
        'is_managers_service_request_closed_notification_enabled',
        'is_auditors_service_request_created_email_enabled',
        'is_auditors_service_request_created_notification_enabled',
        'is_auditors_service_request_assigned_email_enabled',
        'is_auditors_service_request_assigned_notification_enabled',
        'is_auditors_service_request_closed_email_enabled',
        'is_auditors_service_request_closed_notification_enabled',
        'last_assigned_id',
        'is_managers_service_request_update_email_enabled',
        'is_managers_service_request_update_notification_enabled',
        'is_managers_service_request_status_change_email_enabled',
        'is_managers_service_request_status_change_notification_enabled',
        'is_auditors_service_request_update_email_enabled',
        'is_auditors_service_request_update_notification_enabled',
        'is_auditors_service_request_status_change_email_enabled',
        'is_auditors_service_request_status_change_notification_enabled',
        'is_customers_service_request_created_email_enabled',
        'is_customers_service_request_created_notification_enabled',
        'is_customers_service_request_assigned_email_enabled',
        'is_customers_service_request_assigned_notification_enabled',
        'is_customers_service_request_update_email_enabled',
        'is_customers_service_request_update_notification_enabled',
        'is_customers_service_request_status_change_email_enabled',
        'is_customers_service_request_status_change_notification_enabled',
        'is_customers_service_request_closed_email_enabled',
        'is_customers_service_request_closed_notification_enabled',
        'is_customers_service_request_closed_notification_enabled',
        'is_customers_survey_response_email_enabled',
        'is_email_automatic_creation_enabled',
        'is_email_automatic_creation_contact_create_enabled',
        'email_automatic_creation_priority_id',
        'email_automatic_creation_bcc',
        'is_reminders_enabled',
        'sort',
        'category_id',
        'is_ai_clarification_enabled',
        'is_ai_resolution_enabled',
    ];

    public function serviceRequests(): HasManyThrough
    {
        return $this->through('priorities')->has('serviceRequests');
    }

    /**
     * @return HasMany<ServiceRequestPriority, $this>
     */
    public function priorities(): HasMany
    {
        return $this->hasMany(ServiceRequestPriority::class, 'type_id');
    }

    /**
     * @return HasOne<ServiceRequestForm, $this>
     */
    public function form(): HasOne
    {
        return $this->hasOne(ServiceRequestForm::class, 'service_request_type_id')
            ->withoutArchived();
    }

    /**
     * @return BelongsToMany<User, $this, covariant ServiceRequestTypeUserManager>
     */
    public function managerUsers(): BelongsToMany
    {
        return $this->belongsToMany(
            related: User::class,
            table: 'service_request_type_manager_users',
        )
            ->using(ServiceRequestTypeUserManager::class)
            ->withPivot('id')
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany<Team, $this, covariant ServiceRequestTypeTeamManager>
     */
    public function managerTeams(): BelongsToMany
    {
        return $this->belongsToMany(
            related: Team::class,
            table: (new ServiceRequestTypeTeamManager())->getTable(),
        )
            ->using(ServiceRequestTypeTeamManager::class)
            ->withPivot('id')
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany<User, $this, covariant ServiceRequestTypeUserAuditor>
     */
    public function auditorUsers(): BelongsToMany
    {
        return $this->belongsToMany(
            related: User::class,
            table: 'service_request_type_auditor_users',
        )
            ->using(ServiceRequestTypeUserAuditor::class)
            ->withPivot('id')
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany<Team, $this, covariant ServiceRequestTypeTeamAuditor>
     */
    public function auditorTeams(): BelongsToMany
    {
        return $this->belongsToMany(
            related: Team::class,
            table: (new ServiceRequestTypeTeamAuditor())->getTable(),
        )
            ->using(ServiceRequestTypeTeamAuditor::class)
            ->withPivot('id')
            ->withTimestamps();
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function assignmentTypeIndividual(): BelongsTo
    {
        return $this->belongsTo(
            related: User::class,
            foreignKey: 'assignment_type_individual_id',
            relation: 'serviceRequestTypeIndividualAssignment',
        );
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function lastAssignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_assigned_id', 'id');
    }

    /**
     * @return HasMany<ServiceRequestTypeEmailTemplate, $this>
     */
    public function templates(): HasMany
    {
        return $this->hasMany(ServiceRequestTypeEmailTemplate::class, 'service_request_type_id');
    }

    /**
     * @return HasOne<TenantServiceRequestTypeDomain, $this>
     */
    public function domain(): HasOne
    {
        return $this->hasOne(TenantServiceRequestTypeDomain::class, 'service_request_type_id', 'id');
    }

    /**
     * @return BelongsTo<ServiceRequestPriority, $this>
     */
    public function emailAutomaticCreationPriority(): BelongsTo
    {
        return $this->belongsTo(ServiceRequestPriority::class, 'email_automatic_creation_priority_id', 'id');
    }

    /**
     * @return BelongsTo<ServiceRequestTypeCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceRequestTypeCategory::class, 'category_id');
    }

    /**
     * TODO: ServiceRequestCategoryRenameFeature Cleanup - After ServiceRequestCategoryRenameFeature is removed: rename 'default_category' cast back to $casts property and remove 'default_issue_category' from $fillable.
     */
    protected function casts(): array
    {
        return [
            'has_enabled_feedback_collection' => 'boolean',
            'has_enabled_csat' => 'boolean',
            'has_enabled_nps' => 'boolean',
            (ServiceRequestCategoryRenameFeature::active() ? 'default_category' : 'default_issue_category') => ServiceRequestCategory::class,
            'assignment_type' => ServiceRequestTypeAssignmentTypes::class,
            'is_managers_service_request_created_email_enabled' => 'boolean',
            'is_managers_service_request_created_notification_enabled' => 'boolean',
            'is_managers_service_request_assigned_email_enabled' => 'boolean',
            'is_managers_service_request_assigned_notification_enabled' => 'boolean',
            'is_managers_service_request_closed_email_enabled' => 'boolean',
            'is_managers_service_request_closed_notification_enabled' => 'boolean',
            'is_auditors_service_request_created_email_enabled' => 'boolean',
            'is_auditors_service_request_created_notification_enabled' => 'boolean',
            'is_auditors_service_request_assigned_email_enabled' => 'boolean',
            'is_auditors_service_request_assigned_notification_enabled' => 'boolean',
            'is_auditors_service_request_closed_email_enabled' => 'boolean',
            'is_auditors_service_request_closed_notification_enabled' => 'boolean',
            'is_managers_service_request_update_email_enabled' => 'boolean',
            'is_managers_service_request_update_notification_enabled' => 'boolean',
            'is_managers_service_request_status_change_email_enabled' => 'boolean',
            'is_managers_service_request_status_change_notification_enabled' => 'boolean',
            'is_auditors_service_request_update_email_enabled' => 'boolean',
            'is_auditors_service_request_update_notification_enabled' => 'boolean',
            'is_auditors_service_request_status_change_email_enabled' => 'boolean',
            'is_auditors_service_request_status_change_notification_enabled' => 'boolean',
            'is_customers_service_request_created_email_enabled' => 'boolean',
            'is_customers_service_request_created_notification_enabled' => 'boolean',
            'is_customers_service_request_assigned_email_enabled' => 'boolean',
            'is_customers_service_request_assigned_notification_enabled' => 'boolean',
            'is_customers_service_request_update_email_enabled' => 'boolean',
            'is_customers_service_request_update_notification_enabled' => 'boolean',
            'is_customers_service_request_status_change_email_enabled' => 'boolean',
            'is_customers_service_request_status_change_notification_enabled' => 'boolean',
            'is_customers_service_request_closed_email_enabled' => 'boolean',
            'is_customers_service_request_closed_notification_enabled' => 'boolean',
            'is_customers_survey_response_email_enabled' => 'boolean',
            'is_email_automatic_creation_enabled' => 'boolean',
            'is_email_automatic_creation_contact_create_enabled' => 'boolean',
            'is_reminders_enabled' => 'boolean',
            'sort' => 'integer',
            'is_ai_clarification_enabled' => 'boolean',
            'is_ai_resolution_enabled' => 'boolean',
        ];
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(config('project.datetime_format') ?? 'Y-m-d H:i:s');
    }
}
