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

namespace AidingApp\Team\Models;

use AidingApp\Division\Models\Division;
use AidingApp\Project\Models\Project;
use AidingApp\Project\Models\ProjectAuditorTeam;
use AidingApp\Project\Models\ProjectManagerTeam;
use AidingApp\ServiceManagement\Models\ServiceMonitoringTarget;
use AidingApp\ServiceManagement\Models\ServiceMonitoringTargetTeam;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeAuditor;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeManager;
use AidingApp\Team\Database\Factories\TeamFactory;
use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperTeam
 */
class Team extends BaseModel
{
    /** @use HasFactory<TeamFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * @return HasMany<User, $this>
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * @return BelongsToMany<ServiceMonitoringTarget, $this, covariant ServiceMonitoringTargetTeam>
     */
    public function serviceMonitoringTargets(): BelongsToMany
    {
        return $this->belongsToMany(ServiceMonitoringTarget::class)
            ->using(ServiceMonitoringTargetTeam::class)
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany<ServiceRequestType, $this, covariant ServiceRequestTypeManager>
     */
    public function manageableServiceRequestTypes(): BelongsToMany
    {
        return $this->belongsToMany(ServiceRequestType::class, 'service_request_type_managers')
            ->using(ServiceRequestTypeManager::class)
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany<ServiceRequestType, $this, covariant ServiceRequestTypeAuditor>
     */
    public function auditableServiceRequestTypes(): BelongsToMany
    {
        return $this->belongsToMany(ServiceRequestType::class, 'service_request_type_auditors')
            ->using(ServiceRequestTypeAuditor::class)
            ->withTimestamps();
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    /**
    * @return BelongsToMany<Project, $this, ProjectManagerTeam>
    */
    public function manageProjects(): BelongsToMany
    {
        return $this
            ->belongsToMany(Project::class, 'project_manager_teams', 'team_id', 'project_id')
            ->using(ProjectManagerTeam::class)
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany<Project, $this, ProjectAuditorTeam>
     */
    public function auditProjects(): BelongsToMany
    {
        return $this
            ->belongsToMany(Project::class, 'project_auditor_teams', 'team_id', 'project_id')
            ->using(ProjectAuditorTeam::class)
            ->withTimestamps();
    }
}
