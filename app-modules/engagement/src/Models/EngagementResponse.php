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
use AidingApp\Engagement\Enums\EngagementResponseType;
use AidingApp\Engagement\Models\Contracts\HasDeliveryMethod;
use AidingApp\Engagement\Observers\EngagementResponseObserver;
use AidingApp\Notification\Enums\NotificationChannel;
use AidingApp\Timeline\Models\Contracts\ProvidesATimeline;
use AidingApp\Timeline\Models\Timeline;
use AidingApp\Timeline\Timelines\EngagementResponseTimeline;
use App\Models\BaseModel;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

/**
 * @mixin IdeHelperEngagementResponse
 */
#[ObservedBy([EngagementResponseObserver::class])]
class EngagementResponse extends BaseModel implements Auditable, ProvidesATimeline, HasDeliveryMethod, HasMedia
{
    use AuditableTrait;
    use SoftDeletes;
    use InteractsWithMedia;

    protected $fillable = [
        'sender_id',
        'sender_type',
        'content',
        'sent_at',
        'subject',
        'type',
        'raw',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'type' => EngagementResponseType::class,
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('attachments');
    }

    /**
     * @return MorphOne<Timeline, $this>
     */
    public function timelineRecord(): MorphOne
    {
        return $this->morphOne(Timeline::class, 'timelineable');
    }

    public function timeline(): EngagementResponseTimeline
    {
        return new EngagementResponseTimeline($this);
    }

    public static function getTimelineData(Model $forModel): Collection
    {
        return $forModel->orderedEngagementResponses()->get();
    }

    public function sender(): MorphTo
    {
        return $this->morphTo(
            name: 'sender',
            type: 'sender_type',
            id: 'sender_id',
        );
    }

    public function scopeSentByContact(Builder $query): void
    {
        $query->where('sender_type', resolve(Contact::class)->getMorphClass());
    }

    public function getBody(): HtmlString
    {
        $content = $this->content;

        if (preg_match('/<body[^>]*>(.*?)<\/body>/is', $content, $matches)) {
            $content = $matches[1];
        }

        return str(
            (new HtmlSanitizer(
                (new HtmlSanitizerConfig())
                    ->allowSafeElements()
                    ->forceHttpsUrls()
                    ->withMaxInputLength(500000)
            ))
                ->sanitize($content)
        )
            ->toHtmlString();
    }

    public function getDeliveryMethod(): NotificationChannel
    {
        return match ($this->type) {
            EngagementResponseType::Email => NotificationChannel::Email,
        };
    }
}
