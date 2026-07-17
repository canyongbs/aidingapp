<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Aiding App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Aiding App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AidingApp\Project\Models;

use AidingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use AidingApp\InventoryManagement\Models\Asset;
use AidingApp\Project\Database\Factories\PipelineEntryFactory;
use AidingApp\Project\Observers\PipelineEntryObserver;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @property string|null $assigned_to_type
 * @property string|null $assigned_to_id
 * @property string|null $created_by
 * @property bool $is_visible_to_guests
 *
 * @mixin IdeHelperPipelineEntry
 */
#[ObservedBy([PipelineEntryObserver::class])]
class PipelineEntry extends Model implements Auditable
{
    /** @use HasFactory<PipelineEntryFactory> */
    use HasFactory;

    use HasUuids;
    use AuditableTrait;

    protected $table = 'pipeline_entries';

    protected $fillable = [
        'name',
        'pipeline_stage_id',
        'organizable_id',
        'organizable_type',
        'description',
        'due',
        'assigned_to_id',
        'assigned_to_type',
        'created_by',
        'is_visible_to_guests',
    ];

    protected $casts = [
        'due' => 'datetime',
        'is_visible_to_guests' => 'boolean',
    ];

    /**
     * @return BelongsTo<PipelineStage, $this>
     */
    public function pipelineStage(): BelongsTo
    {
        return $this->belongsTo(PipelineStage::class);
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function organizable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function assignedTo(): MorphTo
    {
        return $this->morphTo('assigned_to', 'assigned_to_type', 'assigned_to_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return BelongsToMany<ProjectMilestone, $this, PipelineEntryMilestone>
     */
    public function milestones(): BelongsToMany
    {
        return $this
            ->belongsToMany(ProjectMilestone::class, 'pipeline_entry_milestones', 'pipeline_entry_id', 'project_milestone_id')
            ->using(PipelineEntryMilestone::class)
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany<Asset, $this, PipelineEntryAsset>
     */
    public function assets(): BelongsToMany
    {
        return $this
            ->belongsToMany(Asset::class, 'pipeline_entry_assets', 'pipeline_entry_id', 'asset_id')
            ->using(PipelineEntryAsset::class)
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany<ServiceRequest, $this, PipelineEntryServiceRequest>
     */
    public function serviceRequests(): BelongsToMany
    {
        return $this
            ->belongsToMany(ServiceRequest::class, 'pipeline_entry_service_requests', 'pipeline_entry_id', 'service_request_id')
            ->using(PipelineEntryServiceRequest::class)
            ->withTimestamps();
    }
}
