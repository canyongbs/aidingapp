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

namespace AidingApp\Engagement\Models;

use AidingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use AidingApp\Contact\Models\Contact;
use AidingApp\Engagement\Actions\GenerateEngagementBodyContent;
use AidingApp\Engagement\Models\Contracts\HasDeliveryMethod;
use AidingApp\Engagement\Observers\EngagementObserver;
use AidingApp\Notification\Enums\NotificationChannel;
use AidingApp\Notification\Models\EmailMessage;
use AidingApp\Timeline\Models\Contracts\ProvidesATimeline;
use AidingApp\Timeline\Models\Timeline;
use AidingApp\Timeline\Timelines\EngagementTimeline;
use App\Models\BaseModel;
use App\Models\Concerns\BelongsToEducatable;
use App\Models\Contracts\Educatable;
use App\Models\Scopes\LicensedToEducatable;
use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @property-read Educatable $recipient
 *
 * @mixin IdeHelperEngagement
 */
#[ObservedBy([EngagementObserver::class])]
class Engagement extends BaseModel implements Auditable, ProvidesATimeline, HasDeliveryMethod, HasMedia
{
    use AuditableTrait;
    use BelongsToEducatable;
    use InteractsWithMedia;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'engagement_batch_id',
        'subject',
        'body',
        'recipient_id',
        'recipient_type',
        'scheduled_at',
        'dispatched_at',
        'channel',
    ];

    protected $casts = [
        'body' => 'array',
        'scheduled_at' => 'datetime',
        'dispatched_at' => 'datetime',
        'channel' => NotificationChannel::class,
    ];

    /**
     * @return MorphOne<Timeline, $this>
     */
    public function timelineRecord(): MorphOne
    {
        return $this->morphOne(Timeline::class, 'timelineable');
    }

    public function timeline(): EngagementTimeline
    {
        return new EngagementTimeline($this);
    }

    /**
     * @return Collection<int, $this>
     */
    public static function getTimelineData(Model $forModel): Collection
    {
        return $forModel->orderedEngagements()->with(['latestEmailMessage', 'batch'])->get();
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function createdBy(): BelongsTo
    {
        return $this->user();
    }

    /**
     * @return MorphMany<EmailMessage, $this>
     */
    public function emailMessages(): MorphMany
    {
        return $this->morphMany(
            related: EmailMessage::class,
            name: 'related',
            type: 'related_type',
            id: 'related_id',
            localKey: 'id',
        );
    }

    /**
     * @return MorphOne<EmailMessage, $this>
     */
    public function latestEmailMessage(): MorphOne
    {
        return $this->morphOne(EmailMessage::class, 'related')->latestOfMany();
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function recipient(): MorphTo
    {
        return $this->morphTo(
            name: 'recipient',
            type: 'recipient_type',
            id: 'recipient_id',
        );
    }

    /**
     * @return BelongsTo<EngagementBatch, $this>
     */
    public function engagementBatch(): BelongsTo
    {
        return $this->belongsTo(EngagementBatch::class);
    }

    /**
     * @return BelongsTo<EngagementBatch, $this>
     */
    public function batch(): BelongsTo
    {
        return $this->engagementBatch();
    }

    /**
     * @param Builder<$this> $query
     *
     * @return void
     */
    public function scopeIsNotPartOfABatch(Builder $query): void
    {
        $query->whereNull('engagement_batch_id');
    }

    /**
     * @param Builder<$this> $query
     *
     * @return void
     */
    public function scopeSentToContact(Builder $query): void
    {
        $query->where('recipient_type', resolve(Contact::class)->getMorphClass());
    }

    public function getBody(): HtmlString
    {
        return app(GenerateEngagementBodyContent::class)(
            $this->body,
            $this->getMergeData(),
            $this->batch ?? $this,
            'body',
        );
    }

    /**
     * @return array<string, string>
     */
    public function getMergeData(): array
    {
        /** @var Contact $contact */
        $contact = $this->recipient;

        return [
            'contact full name' => $contact->getAttribute($contact->displayNameKey()),
            'contact email' => $contact->getAttribute($contact->displayEmailKey()),
        ];
    }

    /**
     * @param class-string $type
     *
     * @return array<string>
     */
    public static function getMergeTags(string $type): array
    {
        return match ($type) {
            Contact::class => [
                'contact full name',
                'contact email',
            ],
            default => [],
        };
    }

    public function getDeliveryMethod(): NotificationChannel
    {
        return $this->channel;
    }

    protected static function booted(): void
    {
        static::addGlobalScope('licensed', function (Builder $builder) {
            $builder->tap(new LicensedToEducatable('recipient'));
        });
    }
}
