<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

use App\Models\User;
use DateTimeInterface;
use AidingApp\Task\Models\Task;
use App\Models\Authenticatable;
use AidingApp\Alert\Models\Alert;
use App\Models\Scopes\HasLicense;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Contracts\Educatable;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use AidingApp\Authorization\Enums\LicenseType;
use AidingApp\Engagement\Models\EngagementFile;
use AidingApp\Notification\Models\Subscription;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use AidingApp\InventoryManagement\Models\AssetCheckIn;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use AidingApp\Engagement\Models\EngagementFileEntities;
use AidingApp\InventoryManagement\Models\AssetCheckOut;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use AidingApp\Contact\Filament\Resources\ContactResource;
use AidingApp\Notification\Models\Contracts\Subscribable;
use Illuminate\Foundation\Auth\User as BaseAuthenticatable;
use AidingApp\Notification\Models\Concerns\HasSubscriptions;
use AidingApp\Notification\Models\Concerns\NotifiableViaSms;
use AidingApp\Timeline\Models\Contracts\HasFilamentResource;
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;
use AidingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use AidingApp\Notification\Models\Contracts\NotifiableInterface;
use AidingApp\Engagement\Models\Concerns\HasManyMorphedEngagements;
use AidingApp\Engagement\Models\Concerns\HasManyMorphedEngagementResponses;

/**
 * @property string $display_name
 *
 * @mixin IdeHelperContact
 */
class Contact extends BaseAuthenticatable implements Auditable, Subscribable, Educatable, HasFilamentResource, NotifiableInterface
{
    use AuditableTrait;
    use HasFactory;
    use HasManyMorphedEngagementResponses;
    use HasManyMorphedEngagements;
    use HasSubscriptions;
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

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function serviceRequests(): MorphMany
    {
        return $this->morphMany(
            related: ServiceRequest::class,
            name: 'respondent',
            type: 'respondent_type',
            id: 'respondent_id',
            localKey: 'id'
        );
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(ContactStatus::class);
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(ContactSource::class);
    }

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

    public function tasks(): MorphMany
    {
        return $this->morphMany(Task::class, 'concern');
    }

    public function alerts(): MorphMany
    {
        return $this->morphMany(Alert::class, 'concern');
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

    public function subscribedUsers(): MorphToMany
    {
        return $this->morphToMany(
            related: User::class,
            name: 'subscribable',
            table: 'subscriptions',
        )
            ->using(Subscription::class)
            ->withPivot('id')
            ->withTimestamps()
            ->tap(new HasLicense($this->getLicenseType()));
    }

    public function assetCheckIns(): MorphMany
    {
        return $this->morphMany(
            related: AssetCheckIn::class,
            name: 'checked_in_from',
            type: 'checked_in_from_type',
            id: 'checked_in_from_id',
            localKey: 'id'
        );
    }

    public function assetCheckOuts(): MorphMany
    {
        return $this->morphMany(
            related: AssetCheckOut::class,
            name: 'checked_out_to',
            type: 'checked_out_to_type',
            id: 'checked_out_to_id',
            localKey: 'id'
        );
    }

    public static function getLicenseType(): LicenseType
    {
        return LicenseType::RecruitmentCrm;
    }

    public function organizations(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_id', 'id');
    }

    protected static function booted(): void
    {
        static::addGlobalScope('licensed', function (Builder $builder) {
            if (! auth()->check()) {
                return;
            }

            /** @var Authenticatable $user */
            $user = auth()->user();

            if (! $user->hasLicense(Contact::getLicenseType())) {
                $builder->whereRaw('1 = 0');
            }
        });
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(config('project.datetime_format') ?? 'Y-m-d H:i:s');
    }

    protected function displayName(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value, array $attributes) => $attributes[$this->displayNameKey()],
        );
    }
}
