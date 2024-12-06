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

namespace AidingApp\InventoryManagement\Models;

use App\Models\BaseModel;
use Illuminate\Support\Collection;
use AidingApp\Timeline\Models\Timeline;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use AidingApp\Timeline\Timelines\AssetCheckOutTimeline;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use AidingApp\Timeline\Models\Contracts\ProvidesATimeline;
use AidingApp\InventoryManagement\Enums\AssetCheckOutStatus;
use AidingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use AidingApp\InventoryManagement\Observers\AssetCheckOutObserver;

/**
 * @mixin IdeHelperAssetCheckOut
 */
#[ObservedBy([AssetCheckOutObserver::class])]
class AssetCheckOut extends BaseModel implements Auditable, ProvidesATimeline
{
    use AuditableTrait;
    use SoftDeletes;

    protected $fillable = [
        'asset_id',
        'asset_check_in_id',
        'checked_out_by_type',
        'checked_out_by_id',
        'checked_out_to_type',
        'checked_out_to_id',
        'checked_out_at',
        'expected_check_in_at',
        'notes',
    ];

    protected $casts = [
        'checked_out_at' => 'datetime',
        'expected_check_in_at' => 'datetime',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function checkedOutBy(): MorphTo
    {
        return $this->morphTo(
            name: 'checked_out_by',
            type: 'checked_out_by_type',
            id: 'checked_out_by_id',
        );
    }

    public function checkedOutTo(): MorphTo
    {
        return $this->morphTo(
            name: 'checked_out_to',
            type: 'checked_out_to_type',
            id: 'checked_out_to_id',
        );
    }

    public function checkIn(): BelongsTo
    {
        return $this->belongsTo(AssetCheckIn::class, 'asset_check_in_id');
    }

    public function timelineRecord(): MorphOne
    {
        return $this->morphOne(Timeline::class, 'timelineable');
    }

    public function timeline(): AssetCheckOutTimeline
    {
        return new AssetCheckOutTimeline($this);
    }

    public static function getTimelineData(Model $forModel): Collection
    {
        return $forModel->checkOuts()->get();
    }

    public function scopeWithoutReturned(Builder $query): Builder
    {
        return $query->whereNull('asset_check_in_id');
    }

    protected function status(): Attribute
    {
        return Attribute::get(function () {
            if ($this->checkIn()->exists()) {
                return AssetCheckOutStatus::Returned;
            }

            if ($this->expected_check_in_at->isPast()) {
                return AssetCheckOutStatus::PastDue;
            }

            return AssetCheckOutStatus::Active;
        });
    }
}
