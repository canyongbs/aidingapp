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
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use AidingApp\Timeline\Timelines\AssetCheckInTimeline;
use AidingApp\Timeline\Models\Contracts\ProvidesATimeline;
use AidingApp\Audit\Models\Concerns\Auditable as AuditableTrait;

/**
 * @mixin IdeHelperAssetCheckIn
 */
class AssetCheckIn extends BaseModel implements Auditable, ProvidesATimeline
{
    use AuditableTrait;
    use SoftDeletes;

    protected $fillable = [
        'asset_id',
        'checked_in_by_type',
        'checked_in_by_id',
        'checked_in_from_type',
        'checked_in_from_id',
        'checked_in_at',
        'notes',
    ];

    protected $casts = [
        'checked_in_at' => 'datetime',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function checkedInBy(): MorphTo
    {
        return $this->morphTo(
            name: 'checked_in_by',
            type: 'checked_in_by_type',
            id: 'checked_in_by_id',
        );
    }

    public function checkedInFrom(): MorphTo
    {
        return $this->morphTo(
            name: 'checked_in_from',
            type: 'checked_in_from_type',
            id: 'checked_in_from_id',
        );
    }

    public function checkOut(): HasOne
    {
        return $this->hasOne(AssetCheckOut::class);
    }

    public function timelineRecord(): MorphOne
    {
        return $this->morphOne(Timeline::class, 'timelineable');
    }

    public function timeline(): AssetCheckInTimeline
    {
        return new AssetCheckInTimeline($this);
    }

    public static function getTimelineData(Model $forModel): Collection
    {
        return $forModel->checkIns()->get();
    }
}
