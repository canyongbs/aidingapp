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
use AidingApp\Contact\Models\Contact;
use AidingApp\Contact\Models\Organization;
use AidingApp\Department\Models\Department;
use AidingApp\Project\Database\Factories\ProjectFactory;
use AidingApp\Project\Models\Scopes\ProjectVisibilityScope;
use AidingApp\Project\Observers\ProjectObserver;
use App\Models\BaseModel;
use App\Models\User;
use CanyonGBS\Common\Enums\Color;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @mixin IdeHelperProject
 */
#[ObservedBy([ProjectObserver::class])]
#[ScopedBy(ProjectVisibilityScope::class)]
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
        'icon',
        'color',
        'department_id',
        'start_date',
        'target_completion_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'target_completion_date' => 'date',
        'color' => Color::class,
    ];

    /**
     * @return MorphTo<Model, $this>
     */
    public function createdBy(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return BelongsTo<Department, $this>
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
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
     * @return BelongsToMany<Department, $this, ProjectManagerDepartment>
     */
    public function managerDepartments(): BelongsToMany
    {
        return $this
            ->belongsToMany(
                Department::class,
                'project_manager_departments',
                'project_id',
                'department_id',
            )
            ->using(ProjectManagerDepartment::class)
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
     * @return BelongsToMany<Department, $this, ProjectAuditorDepartment>
     */
    public function auditorDepartments(): BelongsToMany
    {
        return $this
            ->belongsToMany(
                Department::class,
                'project_auditor_departments',
                'project_id',
                'department_id',
            )
            ->using(ProjectAuditorDepartment::class)
            ->withTimestamps();
    }

    /**
     * @return HasMany<ProjectFile, $this>
     */
    public function files(): HasMany
    {
        return $this->hasMany(ProjectFile::class, 'project_id');
    }

    /**
     * @return HasMany<ProjectMilestone, $this>
     */
    public function milestones(): HasMany
    {
        return $this->hasMany(ProjectMilestone::class, 'project_id');
    }

    /**
     * @return HasMany<Pipeline, $this>
     */
    public function pipelines(): HasMany
    {
        return $this->hasMany(Pipeline::class, 'project_id');
    }

    /**
     * @return BelongsToMany<Contact, $this, ProjectGuest>
     */
    public function guestContacts(): BelongsToMany
    {
        return $this
            ->morphedByMany(Contact::class, 'guest', 'project_guests', 'project_id', 'guest_id')
            ->using(ProjectGuest::class)
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany<Organization, $this, ProjectGuest>
     */
    public function guestOrganizations(): BelongsToMany
    {
        return $this
            ->morphedByMany(Organization::class, 'guest', 'project_guests', 'project_id', 'guest_id')
            ->using(ProjectGuest::class)
            ->withTimestamps();
    }

    /**
     * @return array<string, mixed>
     */
    public function getGradient(): array
    {
        $gradientColors = [
            'gray' => ['from' => '#9ca3af', 'to' => '#374151'],
            'red' => ['from' => '#fca5a5', 'to' => '#b91c1c'],
            'orange' => ['from' => '#fdba74', 'to' => '#c2410c'],
            'amber' => ['from' => '#fcd34d', 'to' => '#b45309'],
            'yellow' => ['from' => '#fde047', 'to' => '#a16207'],
            'lime' => ['from' => '#bef264', 'to' => '#4d7c0f'],
            'green' => ['from' => '#86efac', 'to' => '#15803d'],
            'emerald' => ['from' => '#6ee7b7', 'to' => '#047857'],
            'teal' => ['from' => '#5eead4', 'to' => '#0f766e'],
            'cyan' => ['from' => '#67e8f9', 'to' => '#0e7490'],
            'sky' => ['from' => '#7dd3fc', 'to' => '#0369a1'],
            'blue' => ['from' => '#93c5fd', 'to' => '#1d4ed8'],
            'indigo' => ['from' => '#a5b4fc', 'to' => '#4338ca'],
            'violet' => ['from' => '#c4b5fd', 'to' => '#6d28d9'],
            'purple' => ['from' => '#d8b4fe', 'to' => '#7e22ce'],
            'fuchsia' => ['from' => '#f0abfc', 'to' => '#a21caf'],
            'pink' => ['from' => '#f9a8d4', 'to' => '#be185d'],
            'rose' => ['from' => '#fda4af', 'to' => '#be123c'],
        ];

        return $gradientColors[$this->color->value ?? 'blue'] ?? $gradientColors['blue'];
    }
}
