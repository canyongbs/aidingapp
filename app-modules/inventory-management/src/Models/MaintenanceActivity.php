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

namespace AidingApp\InventoryManagement\Models;

use AidingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use AidingApp\InventoryManagement\Database\Factories\MaintenanceActivityFactory;
use AidingApp\InventoryManagement\Enums\MaintenanceActivityStatus;
use AidingApp\InventoryManagement\Observers\MaintenanceActivityObserver;
use AidingApp\Timeline\Models\Contracts\ProvidesATimeline;
use AidingApp\Timeline\Models\Timeline;
use AidingApp\Timeline\Timelines\MaintenanceActivityTimeline;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @mixin IdeHelperMaintenanceActivity
 */
#[ObservedBy([MaintenanceActivityObserver::class])]
class MaintenanceActivity extends BaseModel implements Auditable, ProvidesATimeline
{
    use AuditableTrait;
    use SoftDeletes;

    /** @use HasFactory<MaintenanceActivityFactory> */
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'completed_date',
        'details',
        'maintenance_provider_id',
        'notes',
        'scheduled_date',
        'status',
    ];

    protected $casts = [
        'completed_date' => 'datetime',
        'scheduled_date' => 'datetime',
        'status' => MaintenanceActivityStatus::class,
    ];

    /**
     * @return BelongsTo<Asset, $this>
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * @return BelongsTo<MaintenanceProvider, $this>
     */
    public function maintenanceProvider(): BelongsTo
    {
        return $this->belongsTo(MaintenanceProvider::class);
    }

    /**
     * @return MorphOne<Timeline, $this>
     */
    public function timelineRecord(): MorphOne
    {
        return $this->morphOne(Timeline::class, 'timelineable');
    }

    public function timeline(): MaintenanceActivityTimeline
    {
        return new MaintenanceActivityTimeline($this);
    }

    public static function getTimelineData(Model $forModel): Collection
    {
        return $forModel->maintenanceActivities()->get();
    }

    public function isCompleted(): bool
    {
        return $this->status === MaintenanceActivityStatus::Completed;
    }
}
