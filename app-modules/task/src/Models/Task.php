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

namespace AidingApp\Task\Models;

use AidingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use AidingApp\Contact\Models\Contact;
use AidingApp\Project\Models\Project;
use AidingApp\Task\Database\Factories\TaskFactory;
use AidingApp\Task\Enums\TaskStatus;
use AidingApp\Task\Models\Scopes\TaskConfidentialScope;
use AidingApp\Task\Observers\TaskObserver;
use AidingApp\Team\Models\Team;
use App\Models\BaseModel;
use App\Models\User;
use Bvtterfly\ModelStateMachine\HasStateMachine;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
/**
 * @property-read Contact $concern
 *
 * @mixin IdeHelperTask
 */
#[ObservedBy([TaskObserver::class])] #[ScopedBy([TaskConfidentialScope::class])]
class Task extends BaseModel implements Auditable
{
    /** @use HasFactory<TaskFactory> */
    use HasFactory;

    use HasUuids;
    use AuditableTrait;
    use SoftDeletes;
    use HasStateMachine;

    protected $fillable = [
        'title',
        'description',
        'due',
        'concern_id',
        'is_confidential',
    ];

    protected $casts = [
        'status' => TaskStatus::class,
        'due' => 'datetime',
        'is_confidential' => 'boolean',
    ];

    /**
     * @return array<string>
     */
    public function getStateMachineFields(): array
    {
        return [
            'status',
        ];
    }

    /**
     * @return BelongsTo<Contact, $this>
     */
    public function concern(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'concern_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @param Builder<$this> $query
     *
     * @return void
     */
    public function scopeByNextDue(Builder $query): void
    {
        $query->orderBy('due');
    }

    /**
     * @param Builder<$this> $query
     *
     * @return void
     */
    public function scopeOpen(Builder $query): void
    {
        $query->where('status', '=', TaskStatus::Pending)
            ->orWhere('status', '=', TaskStatus::InProgress);
    }

    /**
     * @return BelongsTo<Project, $this>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    /**
     * @return BelongsToMany<User, $this, covariant TaskConfidentialUser>
     */
    public function confidentialAccessUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'task_confidential_users')
            ->using(TaskConfidentialUser::class)
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany<Team, $this, covariant TaskConfidentialTeam>
     */
    public function confidentialAccessTeams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'task_confidential_teams')
            ->using(TaskConfidentialTeam::class)
            ->withTimestamps();
    }

    // public function confidentialAccessProjects(): BelongsToMany
    // {}
}
