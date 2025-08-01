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

namespace AidingApp\Project\Models;

use AidingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use AidingApp\Project\Database\Factories\ProjectFactory;
use AidingApp\Project\Models\Scopes\ProjectVisibilityScope;
use AidingApp\Project\Observers\ProjectObserver;
use AidingApp\Task\Models\Task;
use AidingApp\Team\Models\Team;
use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

#[ObservedBy([ProjectObserver::class])]
#[ScopedBy(ProjectVisibilityScope::class)]
/**
 * @mixin IdeHelperProject
 */
class Project extends BaseModel implements Auditable
{
    /** @use HasFactory<ProjectFactory> */
    use HasFactory;

    use HasUuids;
    use SoftDeletes;
    use AuditableTrait;

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * @return MorphTo<Model, $this>
     */
    public function createdBy(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return HasMany<Task, $this>
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'project_id');
    }

    /**
    * @return BelongsToMany<User, $this, ProjectManagerUser>
    */
    public function managerUsers(): BelongsToMany
    {
        return $this
            ->belongsToMany(User::class, 'project_manager_users', 'project_id', 'user_id')
            ->using(ProjectManagerUser::class)
            ->withTimestamps();
    }

    /**
    * @return BelongsToMany<Team, $this, ProjectManagerTeam>
    */
    public function managerTeams(): BelongsToMany
    {
        return $this
            ->belongsToMany(Team::class, 'project_manager_teams', 'project_id', 'team_id')
            ->using(ProjectManagerTeam::class)
            ->withTimestamps();
    }

    /**
    * @return BelongsToMany<User, $this, ProjectAuditorUser>
    */
    public function auditorUsers(): BelongsToMany
    {
        return $this
            ->belongsToMany(User::class, 'project_auditor_users', 'project_id', 'user_id')
            ->using(ProjectAuditorUser::class)
            ->withTimestamps();
    }

    /**
    * @return BelongsToMany<Team, $this, ProjectAuditorTeam>
    */
    public function auditorTeams(): BelongsToMany
    {
        return $this
            ->belongsToMany(Team::class, 'project_auditor_teams', 'project_id', 'team_id')
            ->using(ProjectAuditorTeam::class)
            ->withTimestamps();
    }
}
