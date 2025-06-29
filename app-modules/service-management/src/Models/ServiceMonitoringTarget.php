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

namespace AidingApp\ServiceManagement\Models;

use AidingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use AidingApp\ServiceManagement\Database\Factories\ServiceMonitoringTargetFactory;
use AidingApp\ServiceManagement\Enums\ServiceMonitoringFrequency;
use AidingApp\Team\Models\Team;
use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @mixin IdeHelperServiceMonitoringTarget
 */
class ServiceMonitoringTarget extends BaseModel implements Auditable
{
    /** @use HasFactory<ServiceMonitoringTargetFactory> */
    use HasFactory;

    use AuditableTrait;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'domain',
        'frequency',
    ];

    protected $casts = [
        'frequency' => ServiceMonitoringFrequency::class,
    ];

    /**
     * @return HasMany<HistoricalServiceMonitoring, $this>
     */
    public function histories(): HasMany
    {
        return $this->hasMany(HistoricalServiceMonitoring::class);
    }

    /**
     * @return HasOne<HistoricalServiceMonitoring, $this>
     */
    public function latestHistory(): HasOne
    {
        return $this->hasOne(HistoricalServiceMonitoring::class)->latestOfMany();
    }

    /**
     * @return BelongsToMany<Team, $this, covariant ServiceMonitoringTargetTeam>
     */
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class)
            ->using(ServiceMonitoringTargetTeam::class)
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany<User, $this, covariant ServiceMonitoringTargetUser>
     */
    public function users(): BelongsToMany
    {
        return $this
            ->belongsToMany(User::class)
            ->using(ServiceMonitoringTargetUser::class)
            ->withTimestamps();
    }
}
