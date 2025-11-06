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

namespace AidingApp\Contact\Models;

use AidingApp\Alert\Models\Alert;
use AidingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use AidingApp\Authorization\Enums\LicenseType;
use AidingApp\Contact\Database\Factories\ContactFactory;
use AidingApp\Contact\Filament\Resources\ContactResource;
use AidingApp\Contact\Observers\ContactObserver;
use AidingApp\Engagement\Models\Concerns\HasManyMorphedEngagementResponses;
use AidingApp\Engagement\Models\Concerns\HasManyMorphedEngagements;
use AidingApp\Engagement\Models\EngagementFile;
use AidingApp\Engagement\Models\EngagementFileEntities;
use AidingApp\InventoryManagement\Models\AssetCheckIn;
use AidingApp\InventoryManagement\Models\AssetCheckOut;
use AidingApp\LicenseManagement\Models\ProductLicense;
use AidingApp\Notification\Models\Concerns\NotifiableViaSms;
use AidingApp\Notification\Models\Contracts\CanBeNotified;
use AidingApp\Portal\Models\KnowledgeBaseArticleVote;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\Task\Models\Task;
use AidingApp\Timeline\Models\Contracts\HasFilamentResource;
use AidingApp\Timeline\Models\Timeline;
use App\Models\Authenticatable;
use App\Models\Contracts\Educatable;
use App\Models\User;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

/**
 * @property string $display_name
 *
 * @mixin IdeHelperContact
 */
#[ObservedBy([ContactObserver::class])]
class Contact extends Authenticatable implements Auditable, Educatable, HasFilamentResource, CanBeNotified
{
    use AuditableTrait;

    /** @use HasFactory<ContactFactory> */
    use HasFactory;

    use HasManyMorphedEngagementResponses;
    use HasManyMorphedEngagements;
    use HasUuids;
    use Notifiable;
    use NotifiableViaSms;
    use SoftDeletes;
    use UsesTenantConnection;
    use HasApiTokens;

    protected $fillable = [
        'first_name',
        'last_name',
        'full_name',
        'preferred',
        'description',
        'email',
        'mobile',
        'sms_opt_out',
        'email_bounce',
        'status_id',
        'source_id',
        'phone',
        'address',
        'address_2',
        'address_3',
        'city',
        'state',
        'postal',
        'assigned_to_id',
        'created_by_id',
    ];

    protected $casts = [
        'sms_opt_out' => 'boolean',
        'email_bounce' => 'boolean',
    ];

    public function hasLicense(LicenseType|string|array|null $type): bool
    {
        return false;
    }

    public function hasAnyLicense(LicenseType|string|array|null $type): bool
    {
        return false;
    }

    public function isSuperAdmin(): bool
    {
        return false;
    }

    public function canRecieveSms(): bool
    {
        return filled($this->mobile);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<ServiceRequest, $this>
     */
    public function serviceRequests(): HasMany
    {
        return $this->hasMany(ServiceRequest::class, 'respondent_id');
    }

    /**
     * @return MorphOne<Timeline, $this>
     */
    public function timeline(): MorphOne
    {
        return $this->morphOne(Timeline::class, 'entity');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<ContactStatus, $this>
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(ContactStatus::class);
    }

    /**
     * @return BelongsTo<ContactSource, $this>
     */
    public function source(): BelongsTo
    {
        return $this->belongsTo(ContactSource::class);
    }

    /**
     * @return MorphToMany<EngagementFile, $this, covariant EngagementFileEntities>
     */
    public function engagementFiles(): MorphToMany
    {
        return $this->morphToMany(
            related: EngagementFile::class,
            name: 'entity',
            table: 'engagement_file_entities',
            foreignPivotKey: 'entity_id',
            relatedPivotKey: 'engagement_file_id',
            relation: 'engagementFiles',
        )
            ->using(EngagementFileEntities::class)
            ->withTimestamps();
    }

    /**
     * @return HasMany<Task, $this>
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'concern_id');
    }

    /**
     * @return HasMany<Alert, $this>
     */
    public function alerts(): HasMany
    {
        return $this->hasMany(Alert::class, 'concern_id');
    }

    public static function displayNameKey(): string
    {
        return 'full_name';
    }

    public static function displayEmailKey(): string
    {
        return 'email';
    }

    public static function filamentResource(): string
    {
        return ContactResource::class;
    }

    /**
     * @return HasMany<AssetCheckIn, $this>
     */
    public function assetCheckIns(): HasMany
    {
        return $this->hasMany(AssetCheckIn::class, 'checked_in_from_id');
    }

    /**
     * @return HasMany<AssetCheckOut, $this>
     */
    public function assetCheckOuts(): HasMany
    {
        return $this->hasMany(AssetCheckOut::class, 'checked_out_to_id');
    }

    public static function getLicenseType(): LicenseType
    {
        return LicenseType::RecruitmentCrm;
    }

    /**
     * @return BelongsTo<Organization, $this>
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_id', 'id');
    }

    /**
     * @return MorphMany<KnowledgeBaseArticleVote, $this>
     */
    public function knowledgeBaseArticleVotes(): MorphMany
    {
        return $this->morphMany(KnowledgeBaseArticleVote::class, 'voter');
    }

    /**
     * @return HasMany<ProductLicense, $this>
     */
    public function productLicenses(): HasMany
    {
        return $this->hasMany(ProductLicense::class, 'assigned_to');
    }

    protected static function booted(): void
    {
        static::addGlobalScope('licensed', function (Builder $builder) {
            if (! auth()->guard('web')->check()) {
                return;
            }

            /** @var Authenticatable $user */
            $user = auth()->guard('web')->user();

            if (! $user->hasLicense(Contact::getLicenseType())) {
                $builder->whereRaw('1 = 0');
            }
        });
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(config('project.datetime_format') ?? 'Y-m-d H:i:s');
    }

    /**
     * @return Attribute<string, never>
     */
    protected function displayName(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value, array $attributes) => $attributes[$this->displayNameKey()],
        );
    }

    public static function getLabel(): string
    {
        return 'contact';
    }
}
