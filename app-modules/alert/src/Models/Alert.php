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

namespace AidingApp\Alert\Models;

use App\Models\BaseModel;
use AidingApp\Contact\Models\Contact;
use AidingApp\Alert\Enums\AlertStatus;
use AidingApp\Alert\Enums\AlertSeverity;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Scopes\LicensedToEducatable;
use App\Models\Concerns\BelongsToEducatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use AidingApp\Notification\Models\Contracts\Subscribable;
use AidingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use AidingApp\Notification\Models\Contracts\CanTriggerAutoSubscription;

/**
 * @property-read Contact $concern
 *
 * @mixin IdeHelperAlert
 */
class Alert extends BaseModel implements Auditable, CanTriggerAutoSubscription
{
    use SoftDeletes;
    use AuditableTrait;
    use BelongsToEducatable;

    protected $fillable = [
        'concern_id',
        'concern_type',
        'description',
        'severity',
        'status',
        'suggested_intervention',
    ];

    protected $casts = [
        'severity' => AlertSeverity::class,
        'status' => AlertStatus::class,
    ];

    public function concern(): MorphTo
    {
        return $this->morphTo();
    }

    public function getSubscribable(): ?Subscribable
    {
        return $this->concern instanceof Subscribable ? $this->concern : null;
    }

    public function scopeStatus(Builder $query, AlertStatus $status): void
    {
        $query->where('status', $status);
    }

    protected static function booted(): void
    {
        static::addGlobalScope('licensed', function (Builder $builder) {
            $builder->tap(new LicensedToEducatable('concern'));
        });
    }
}
