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

namespace AidingApp\Department\Models;

use AidingApp\Division\Models\Division;
use AidingApp\Project\Models\Project;
use AidingApp\Project\Models\ProjectAuditorDepartment;
use AidingApp\Project\Models\ProjectManagerDepartment;
use AidingApp\ServiceManagement\Models\ServiceMonitoringTarget;
use AidingApp\ServiceManagement\Models\ServiceMonitoringTargetDepartment;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeDepartmentAuditor;
use AidingApp\ServiceManagement\Models\ServiceRequestTypeDepartmentManager;
use AidingApp\Department\Database\Factories\DepartmentFactory;
use App\Features\TeamRenameFeature;
use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperDepartment
 */
class Department extends BaseModel
{
    /** @use HasFactory<DepartmentFactory> */
    use HasFactory;

    public function getTable(): string
    {
        return TeamRenameFeature::active() ? 'departments' : 'teams';
    }

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * @return HasMany<User, $this>
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, TeamRenameFeature::active() ? 'department_id' : 'team_id');
    }

    /**
     * @return BelongsToMany<ServiceMonitoringTarget, $this, covariant ServiceMonitoringTargetDepartment>
     */
    public function serviceMonitoringTargets(): BelongsToMany
    {
        return $this->belongsToMany(
            ServiceMonitoringTarget::class,
            TeamRenameFeature::active() ? 'service_monitoring_target_department' : 'service_monitoring_target_team',
            TeamRenameFeature::active() ? 'department_id' : 'team_id',
            'service_monitoring_target_id',
        )
            ->using(ServiceMonitoringTargetDepartment::class)
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany<ServiceRequestType, $this, covariant ServiceRequestTypeDepartmentManager>
     */
    public function manageableServiceRequestTypes(): BelongsToMany
    {
        return $this->belongsToMany(
            ServiceRequestType::class,
            (new ServiceRequestTypeDepartmentManager())->getTable(),
            TeamRenameFeature::active() ? 'department_id' : 'team_id',
            'service_request_type_id',
        )
            ->using(ServiceRequestTypeDepartmentManager::class)
            ->withPivot('id')
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany<ServiceRequestType, $this, covariant ServiceRequestTypeDepartmentAuditor>
     */
    public function auditableServiceRequestTypes(): BelongsToMany
    {
        return $this->belongsToMany(
            ServiceRequestType::class,
            (new ServiceRequestTypeDepartmentAuditor())->getTable(),
            TeamRenameFeature::active() ? 'department_id' : 'team_id',
            'service_request_type_id',
        )
            ->using(ServiceRequestTypeDepartmentAuditor::class)
            ->withPivot('id')
            ->withTimestamps();
    }

    /**
     * @return BelongsTo<Division, $this>
     */
    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    /**
    * @return BelongsToMany<Project, $this, ProjectManagerDepartment>
    */
    public function managedProjects(): BelongsToMany
    {
        return $this
            ->belongsToMany(
                Project::class,
                TeamRenameFeature::active() ? 'project_manager_departments' : 'project_manager_teams',
                TeamRenameFeature::active() ? 'department_id' : 'team_id',
                'project_id',
            )
            ->using(ProjectManagerDepartment::class)
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany<Project, $this, ProjectAuditorDepartment>
     */
    public function auditedProjects(): BelongsToMany
    {
        return $this
            ->belongsToMany(
                Project::class,
                TeamRenameFeature::active() ? 'project_auditor_departments' : 'project_auditor_teams',
                TeamRenameFeature::active() ? 'department_id' : 'team_id',
                'project_id',
            )
            ->using(ProjectAuditorDepartment::class)
            ->withTimestamps();
    }
}
