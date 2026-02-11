<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

    Aiding App™ is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Aiding App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace App\Models;

use AidingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use AidingApp\Authorization\Models\Role;
use AidingApp\Engagement\Models\Concerns\HasManyEngagementBatches;
use AidingApp\Engagement\Models\Concerns\HasManyEngagements;
use AidingApp\Notification\Models\Contracts\CanBeNotified;
use AidingApp\Project\Models\Project;
use AidingApp\Project\Models\ProjectAuditorUser;
use AidingApp\Project\Models\ProjectManagerUser;
use AidingApp\ServiceManagement\Enums\ServiceRequestAssignmentStatus;
use AidingApp\ServiceManagement\Models\ChangeRequest;
use AidingApp\ServiceManagement\Models\ChangeRequestResponse;
use AidingApp\ServiceManagement\Models\ChangeRequestType;
use AidingApp\ServiceManagement\Models\ServiceMonitoringTarget;
use AidingApp\ServiceManagement\Models\ServiceMonitoringTargetUser;
use AidingApp\ServiceManagement\Models\ServiceRequestAssignment;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\Task\Models\Task;
use AidingApp\Team\Models\Team;
use AidingApp\Timeline\Models\Contracts\HasFilamentResource;
use App\Filament\Resources\UserResource;
use App\Settings\DisplaySettings;
use App\Support\HasAdvancedFilter;
use Database\Factories\UserFactory;
use DateTimeInterface;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Lab404\Impersonate\Models\Impersonate;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Staudenmeir\EloquentHasManyDeep\HasManyDeep;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

/**
 * @mixin IdeHelperUser
 */
class User extends Authenticatable implements HasLocalePreference, FilamentUser, Auditable, HasMedia, HasAvatar, CanBeNotified, HasFilamentResource
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use HasAdvancedFilter;
    use Notifiable;
    use SoftDeletes;
    use HasRelationships;
    use HasUuids;
    use AuditableTrait;
    use HasManyEngagements;
    use HasManyEngagementBatches;
    use Impersonate;
    use InteractsWithMedia;

    protected $hidden = [
        'remember_token',
        'password',
    ];

    protected $casts = [
        'is_external' => 'boolean',
        'is_bio_visible_on_profile' => 'boolean',
        'are_pronouns_visible_on_profile' => 'boolean',
        'are_teams_visible_on_profile' => 'boolean',
        'is_division_visible_on_profile' => 'boolean',
        'email_verified_at' => 'datetime',
        'has_enabled_public_profile' => 'boolean',
        'office_hours_are_enabled' => 'boolean',
        'office_hours' => 'array',
        'out_of_office_is_enabled' => 'boolean',
        'out_of_office_starts_at' => 'datetime',
        'out_of_office_ends_at' => 'datetime',
        'is_email_visible_on_profile' => 'boolean',
        'is_phone_number_visible_on_profile' => 'boolean',
        'working_hours_are_enabled' => 'boolean',
        'are_working_hours_visible_on_profile' => 'boolean',
        'working_hours' => 'array',
    ];

    protected $fillable = [
        'emplid',
        'name',
        'email',
        'password',
        'locale',
        'type',
        'is_external',
        'bio',
        'is_bio_visible_on_profile',
        'are_pronouns_visible_on_profile',
        'avatar_url',
        'are_teams_visible_on_profile',
        'timezone',
        'is_division_visible_on_profile',
        'has_enabled_public_profile',
        'public_profile_slug',
        'office_hours_are_enabled',
        'office_hours',
        'out_of_office_is_enabled',
        'out_of_office_starts_at',
        'out_of_office_ends_at',
        'is_email_visible_on_profile',
        'phone_number',
        'is_phone_number_visible_on_profile',
        'working_hours_are_enabled',
        'are_working_hours_visible_on_profile',
        'working_hours',
        'job_title',
        'work_number',
        'work_extension',
        'mobile',
    ];

    /**
     * @var array<string>
     */
    public array $orderable = [
        'id',
        'emplid',
        'name',
        'email',
        'email_verified_at',
        'locale',
    ];

    /**
     * @var array<string>
     */
    public array $filterable = [
        'id',
        'emplid',
        'name',
        'email',
        'email_verified_at',
        'roles.title',
        'locale',
    ];

    public function canRecieveSms(): bool
    {
        return false;
    }

    /**
     * @return HasManyDeep<Model, $this>
     */
    public function permissionsFromRoles(): HasManyDeep
    {
        return $this->hasManyDeepFromRelations($this->roles(), (new Role())->permissions());
    }

    /**
     * @return HasMany<ServiceRequestAssignment, $this>
     */
    public function serviceRequestAssignments(): HasMany
    {
        return $this->hasMany(ServiceRequestAssignment::class)
            ->where('status', ServiceRequestAssignmentStatus::Active);
    }

    /**
     * @return HasManyDeep<Model, $this>
     */
    public function serviceRequests(): HasManyDeep
    {
        return $this->hasManyDeepFromRelations($this->serviceRequestAssignments(), (new ServiceRequestAssignment())->serviceRequest());
    }

    /**
     * @return HasMany<ChangeRequest, $this>
     */
    public function changeRequests(): HasMany
    {
        return $this->hasMany(ChangeRequest::class, 'created_by');
    }

    /**
     * @return HasMany<ChangeRequestResponse, $this>
     */
    public function changeRequestResponses(): HasMany
    {
        return $this->hasMany(ChangeRequestResponse::class);
    }

    /**
     * @return BelongsToMany<ChangeRequestType, $this>
     */
    public function changeRequestTypes(): BelongsToMany
    {
        return $this->belongsToMany(ChangeRequestType::class);
    }

    /**
     * @return BelongsTo<Pronouns, $this>
     */
    public function pronouns(): BelongsTo
    {
        return $this->belongsTo(Pronouns::class);
    }

    /**
     * @return HasMany<Task, $this>
     */
    public function assignedTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    public function preferredLocale()
    {
        return $this->locale;
    }

    /**
     * @return BelongsTo<Team, $this>
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    /**
     * @return HasMany<ServiceRequestType, $this>
     */
    public function serviceRequestTypeIndividualAssignment(): HasMany
    {
        return $this->hasMany(ServiceRequestType::class, 'assignment_type_individual_id', 'id');
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function canImpersonate(): bool
    {
        return $this->isSuperAdmin();
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole(Authenticatable::SUPER_ADMIN_ROLE);
    }

    public function canBeImpersonated(): bool
    {
        return ! $this->isSuperAdmin();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')
            ->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('avatar-height-250px')
            ->performOnCollections('avatar')
            ->height(250);
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url ?: $this->getFirstTemporaryUrl(now()->addMinutes(5), 'avatar', 'avatar-height-250px');
    }

    public static function filamentResource(): string
    {
        return UserResource::class;
    }

    public static function displayNameKey(): string
    {
        return 'name';
    }

    public function getDynamicContext(): string
    {
        $context = "My name is {$this->name}";

        if ($this->job_title) {
            $context .= " and I am a {$this->job_title}";
        }

        return "{$context}. When you respond please use this information about me to tailor your response.";
    }

    public function assignTeam(string $teamId): void
    {
        $this->team()->associate($teamId)->save();
    }

    /**
     * @return BelongsToMany<ServiceMonitoringTarget, $this, covariant ServiceMonitoringTargetUser>
     */
    public function serviceMonitoringTargets(): BelongsToMany
    {
        return $this->belongsToMany(ServiceMonitoringTarget::class)
            ->using(ServiceMonitoringTargetUser::class)
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany<Project, $this, ProjectManagerUser>
     */
    public function managedProjects(): BelongsToMany
    {
        return $this
            ->belongsToMany(Project::class, 'project_manager_users', 'user_id', 'project_id')
            ->using(ProjectManagerUser::class)
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany<Project, $this, ProjectAuditorUser>
     */
    public function auditedProjects(): BelongsToMany
    {
        return $this
            ->belongsToMany(Project::class, 'project_auditor_users', 'user_id', 'project_id')
            ->using(ProjectAuditorUser::class)
            ->withTimestamps();
    }

    public function getTimezone(): string
    {
        if (filled($userTimezone = $this->timezone)) {
            return $userTimezone;
        }

        if (filled($settingsTimezone = app(DisplaySettings::class)->timezone)) {
            return $settingsTimezone;
        }

        return config('app.timezone');
    }

    public function receivesBroadcastNotificationsOn(): string
    {
        return "user.{$this->getKey()}";
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(config('project.datetime_format') ?? 'Y-m-d H:i:s');
    }
}
