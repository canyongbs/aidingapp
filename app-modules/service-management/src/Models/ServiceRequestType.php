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
use AidingApp\Contact\Models\ContactType;
use AidingApp\Department\Models\Department;
use AidingApp\ServiceManagement\Database\Factories\ServiceRequestTypeFactory;
use AidingApp\ServiceManagement\Enums\EmailAutomaticCreationContactCreateCondition;
use AidingApp\ServiceManagement\Enums\ServiceRequestCategory;
use AidingApp\ServiceManagement\Enums\ServiceRequestEmailTemplateType;
use AidingApp\ServiceManagement\Enums\ServiceRequestNotificationChannel;
use AidingApp\ServiceManagement\Enums\ServiceRequestTypeAssignmentTypes;
use AidingApp\ServiceManagement\Enums\ServiceRequestTypeEmailTemplateRole;
use AidingApp\ServiceManagement\Models\Concerns\RestrictsVisibilityToContactTypes;
use AidingApp\ServiceManagement\Observers\ServiceRequestTypeObserver;
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
 * @mixin IdeHelperServiceRequestType
 */
#[ObservedBy([ServiceRequestTypeObserver::class])]
class ServiceRequestType extends BaseModel implements Auditable
{
    use CanBeArchived;
    use SoftDeletes;
    use HasUuids;
    use AuditableTrait;
    use RestrictsVisibilityToContactTypes;

    /** @use HasFactory<ServiceRequestTypeFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'has_enabled_feedback_collection',
        'has_enabled_csat',
        'has_enabled_nps',
        'description',
        'icon',
        'default_category',
        'assignment_type',
        'last_assigned_id',
        'is_email_automatic_creation_enabled',
        'is_email_automatic_creation_contact_create_enabled',
        'email_automatic_creation_priority_id',
        'email_automatic_creation_bcc',
        'is_reminders_enabled',
        'sort',
        'is_ai_clarification_enabled',
        'is_ai_resolution_enabled',
        'is_live_chat_enabled',
        'max_simultaneous_chats',
        'email_automatic_creation_contact_create_condition',
        'is_visibility_restricted',
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
     * @return BelongsToMany<Department, $this, covariant ServiceRequestTypeDepartmentManager>
     */
    public function managerDepartments(): BelongsToMany
    {
        return $this->belongsToMany(
            related: Department::class,
            table: (new ServiceRequestTypeDepartmentManager())->getTable(),
            foreignPivotKey: 'service_request_type_id',
            relatedPivotKey: 'department_id',
        )
            ->using(ServiceRequestTypeDepartmentManager::class)
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
     * @return BelongsToMany<Department, $this, covariant ServiceRequestTypeDepartmentAuditor>
     */
    public function auditorDepartments(): BelongsToMany
    {
        return $this->belongsToMany(
            related: Department::class,
            table: (new ServiceRequestTypeDepartmentAuditor())->getTable(),
            foreignPivotKey: 'service_request_type_id',
            relatedPivotKey: 'department_id',
        )
            ->using(ServiceRequestTypeDepartmentAuditor::class)
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
     * @return HasMany<ServiceRequestTypeEmailPreference, $this>
     */
    public function emailPreferences(): HasMany
    {
        return $this->hasMany(ServiceRequestTypeEmailPreference::class, 'service_request_type_id');
    }

    public function isPreferenceEnabled(
        ServiceRequestEmailTemplateType $templateType,
        ServiceRequestTypeEmailTemplateRole $role,
        ServiceRequestNotificationChannel $channel,
    ): bool {
        return $this->emailPreferences
            ->first(
                fn (ServiceRequestTypeEmailPreference $preference): bool => $preference->service_request_email_template_type === $templateType
                    && $preference->service_request_email_template_role === $role
                    && $preference->notification_channel === $channel,
            )->is_enabled ?? false;
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
     * @return BelongsToMany<ServiceRequestTypeCategory, $this, covariant ServiceRequestCategoryType>
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(
            related: ServiceRequestTypeCategory::class,
            table: 'service_request_category_types',
            foreignPivotKey: 'service_request_type_id',
            relatedPivotKey: 'service_request_type_category_id',
        )
            ->using(ServiceRequestCategoryType::class)
            ->withPivot('id', 'sort')
            ->withTimestamps()
            ->orderByPivot('sort');
    }

    /**
     * @return BelongsToMany<ContactType, $this, ServiceRequestTypeVisibilityContactType>
     */
    public function restrictedToContactTypes(): BelongsToMany
    {
        return $this->belongsToMany(
            related: ContactType::class,
            table: 'service_request_type_visibility_contact_types',
            foreignPivotKey: 'service_request_type_id',
            relatedPivotKey: 'contact_type_id',
        )
            ->using(ServiceRequestTypeVisibilityContactType::class)
            ->withTimestamps();
    }

    /**
     * @return iterable<ServiceRequestTypeCategory>
     */
    public function visibilityRestrictionParents(): iterable
    {
        return $this->categories;
    }

    /**
     * The ids of every category this type is filed under.
     *
     * @return array<int, string>
     */
    public function categoryIds(): array
    {
        return $this->categories->pluck('id')->all();
    }

    /**
     * The per-area sort of this type keyed by the id of every category it is filed under.
     *
     * The sort comes from the pivot so a type can be ordered differently in each area.
     *
     * @return array<string, int>
     */
    public function categorySortMap(): array
    {
        $map = [];

        foreach ($this->categories as $category) {
            $pivot = $category->getRelationValue('pivot');

            assert($pivot instanceof ServiceRequestCategoryType);

            $map[$category->id] = (int) $pivot->getAttribute('sort');
        }

        return $map;
    }

    protected function casts(): array
    {
        return [
            'has_enabled_feedback_collection' => 'boolean',
            'has_enabled_csat' => 'boolean',
            'has_enabled_nps' => 'boolean',
            'default_category' => ServiceRequestCategory::class,
            'assignment_type' => ServiceRequestTypeAssignmentTypes::class,
            'is_email_automatic_creation_enabled' => 'boolean',
            'is_email_automatic_creation_contact_create_enabled' => 'boolean',
            'is_reminders_enabled' => 'boolean',
            'sort' => 'integer',
            'is_ai_clarification_enabled' => 'boolean',
            'is_ai_resolution_enabled' => 'boolean',
            'is_live_chat_enabled' => 'boolean',
            'max_simultaneous_chats' => 'integer',
            'email_automatic_creation_contact_create_condition' => EmailAutomaticCreationContactCreateCondition::class,
            'is_visibility_restricted' => 'boolean',
        ];
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(config('project.datetime_format') ?? 'Y-m-d H:i:s');
    }
}
