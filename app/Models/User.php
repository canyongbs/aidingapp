<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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
use AidingApp\Authorization\Enums\LicenseType;
use AidingApp\Authorization\Models\License;
use AidingApp\Authorization\Models\Role;
use AidingApp\Contact\Models\Contact;
use AidingApp\Engagement\Models\Concerns\HasManyEngagementBatches;
use AidingApp\Engagement\Models\Concerns\HasManyEngagements;
use AidingApp\InAppCommunication\Models\TwilioConversation;
use AidingApp\InAppCommunication\Models\TwilioConversationUser;
use AidingApp\Notification\Models\Contracts\CanBeNotified;
use AidingApp\Notification\Models\Subscription;
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
use AidingApp\Team\Models\TeamUser;
use AidingApp\Timeline\Models\Contracts\HasFilamentResource;
use App\Filament\Resources\UserResource;
use App\Support\HasAdvancedFilter;
use DateTimeInterface;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Arr;
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

    public $orderable = [
        'id',
        'emplid',
        'name',
        'email',
        'email_verified_at',
        'locale',
    ];

    public $filterable = [
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

    public function conversations(): BelongsToMany
    {
        return $this->belongsToMany(TwilioConversation::class, 'twilio_conversation_user', 'user_id', 'conversation_sid')
            ->withPivot([
                'participant_sid',
                'is_channel_manager',
                'is_pinned',
                'notification_preference',
                'first_unread_message_sid',
                'first_unread_message_at',
                'last_unread_message_content',
                'last_read_at',
                'unread_messages_count',
            ])
            ->withTimestamps()
            ->as('participant')
            ->using(TwilioConversationUser::class);
    }

    public function licenses(): HasMany
    {
        return $this->hasMany(License::class, 'user_id');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function contactSubscriptions(): MorphToMany
    {
        return $this->morphedByMany(
            related: Contact::class,
            name: 'subscribable',
            table: 'subscriptions'
        )
            ->using(Subscription::class)
            ->withPivot('id')
            ->withTimestamps();
    }

    public function contactAlerts(): HasManyDeep
    {
        return $this->hasManyDeepFromRelations($this->contactSubscriptions(), (new Contact())->alerts());
    }

    public function permissionsFromRoles(): HasManyDeep
    {
        return $this->hasManyDeepFromRelations($this->roles(), (new Role())->permissions());
    }

    public function serviceRequestAssignments(): HasMany
    {
        return $this->hasMany(ServiceRequestAssignment::class)
            ->where('status', ServiceRequestAssignmentStatus::Active);
    }

    public function serviceRequests(): HasManyDeep
    {
        return $this->hasManyDeepFromRelations($this->serviceRequestAssignments(), (new ServiceRequestAssignment())->serviceRequest());
    }

    public function changeRequests(): HasMany
    {
        return $this->hasMany(ChangeRequest::class, 'created_by');
    }

    public function changeRequestResponses(): HasMany
    {
        return $this->hasMany(ChangeRequestResponse::class);
    }

    public function changeRequestTypes(): BelongsToMany
    {
        return $this->belongsToMany(ChangeRequestType::class);
    }

    public function pronouns(): BelongsTo
    {
        return $this->belongsTo(Pronouns::class);
    }

    public function assignedTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    public function preferredLocale()
    {
        return $this->locale;
    }

    public function teams(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

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

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('avatar-height-250px')
            ->performOnCollections('avatar')
            ->height(250);
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url ?: $this->getFirstTemporaryUrl(now()->addMinutes(5), 'avatar', 'avatar-height-250px');
    }

    /**
     * @param LicenseType | string | array<LicenseType | string> | null $type
     */
    public function hasLicense(LicenseType | string | array | null $type): bool
    {
        if (blank($type)) {
            return true;
        }

        foreach (Arr::wrap($type) as $type) {
            if (! ($type instanceof LicenseType)) {
                $type = LicenseType::from($type);
            }

            if ($this->licenses->doesntContain('type', $type)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param LicenseType | string | array<LicenseType | string> | null $type
     */
    public function hasAnyLicense(LicenseType | string | array | null $type): bool
    {
        if (blank($type)) {
            return true;
        }

        foreach (Arr::wrap($type) as $type) {
            if (! ($type instanceof LicenseType)) {
                $type = LicenseType::from($type);
            }

            if ($this->licenses->contains('type', $type)) {
                return true;
            }
        }

        return false;
    }

    public static function filamentResource(): string
    {
        return UserResource::class;
    }

    public function grantLicense(LicenseType $type): bool
    {
        if ($this->hasLicense($type)) {
            return false;
        }

        return cache()
            ->lock('licenses', 5)
            ->get(function () use ($type) {
                if (! $type->hasAvailableLicenses()) {
                    return false;
                }

                return (bool) $this->licenses()->create(['type' => $type]);
            });
    }

    public function revokeLicense(LicenseType $type): bool
    {
        return (bool) $this->licenses()->where('type', $type)->delete();
    }

    public function getDynamicContext(): string
    {
        $context = "My name is {$this->name}";

        if ($this->job_title) {
            $context .= " and I am a {$this->job_title}";
        }

        return "{$context}. When you respond please use this information about me to tailor your response.";
    }

    public function assignTeam($teamId)
    {
        $this->teams()->detach();

        $this->teams()->attach($teamId);
    }

    public function serviceMonitoringTargets(): BelongsToMany
    {
        return $this->belongsToMany(ServiceMonitoringTarget::class)
            ->using(ServiceMonitoringTargetUser::class)
            ->withTimestamps();
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(config('project.datetime_format') ?? 'Y-m-d H:i:s');
    }
}
