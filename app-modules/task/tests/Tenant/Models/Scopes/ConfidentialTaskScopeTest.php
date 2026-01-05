<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

use AidingApp\Authorization\Enums\LicenseType;
use AidingApp\Project\Models\Project;
use AidingApp\Task\Models\Scopes\ConfidentialTaskScope;
use AidingApp\Task\Models\Task;
use AidingApp\Team\Models\Team;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Tests\asSuperAdmin;

test('is applied as a global scope to the `Task` model', function () {
    Task::bootHasGlobalScopes();

    expect(Task::hasGlobalScope(ConfidentialTaskScope::class))->toBeTrue();
});

test('users can access public tasks and confidential tasks that they have created', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user);

    $ownedConfidentialTasks = Task::factory()->count(10)->create([
        'is_confidential' => true,
        'created_by' => $user,
    ]);

    $privateTasks = Task::factory()->count(10)->create([
        'is_confidential' => true,
    ]);

    $publicTasks = Task::factory()->count(10)->create([
        'is_confidential' => false,
    ]);

    $tasks = Task::query()->get();

    expect($tasks)->toHaveCount(20);

    expect($tasks->pluck('id'))
        ->toContain(...$publicTasks->pluck('id'))
        ->toContain(...$ownedConfidentialTasks->pluck('id'));

    expect($tasks->pluck('id'))->not->toContain(...$privateTasks->pluck('id'));

    expect($tasks->where('is_confidential', true)->pluck('created_by'))
        ->not->toContain(...$privateTasks->pluck('created_by'));
});

test('users can access confidential tasks if they belong to a team with access', function () {
    $teamUser = User::factory()->licensed(LicenseType::cases())->create();

    $team = Team::factory()->create();

    $teamUser->team()->associate($team)->save();

    actingAs($teamUser);

    $ownedConfidentialTasks = Task::factory()
        ->hasAttached($team, [], 'confidentialAccessTeams')
        ->count(10)
        ->create([
            'is_confidential' => true,
        ]);

    $privateTasks = Task::factory()->count(10)->create([
        'is_confidential' => true,
    ]);

    $publicTasks = Task::factory()->count(10)->create([
        'is_confidential' => false,
    ]);

    $tasks = Task::query()->get();

    expect($tasks)->toHaveCount(20);

    expect($tasks->pluck('id'))
        ->toContain(...$publicTasks->pluck('id'))
        ->toContain(...$ownedConfidentialTasks->pluck('id'));

    expect($tasks->pluck('id'))->not->toContain(...$privateTasks->pluck('id'));

    expect($tasks->where('is_confidential', true)->pluck('created_by'))
        ->not->toContain(...$privateTasks->pluck('created_by'));
});

test('users can access confidential tasks if they are designated access', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user);

    $ownedConfidentialTasks = Task::factory()->hasAttached($user, [], 'confidentialAccessUsers')->count(10)->create([
        'is_confidential' => true,
    ]);

    $privateTasks = Task::factory()->count(10)->create([
        'is_confidential' => true,
    ]);

    $publicTasks = Task::factory()->count(10)->create([
        'is_confidential' => false,
    ]);

    $tasks = Task::query()->get();

    expect($tasks)->toHaveCount(20);

    expect($tasks->pluck('id'))
        ->toContain(...$publicTasks->pluck('id'))
        ->toContain(...$ownedConfidentialTasks->pluck('id'));

    expect($tasks->pluck('id'))->not->toContain(...$privateTasks->pluck('id'));

    expect($tasks->where('is_confidential', true)->pluck('created_by'))
        ->not->toContain(...$privateTasks->pluck('created_by'));
});

test('super admin users can access all confidential tasks', function () {
    asSuperAdmin();

    $privateTasks = Task::factory()->count(10)->create([
        'is_confidential' => true,
    ]);

    $publicTasks = Task::factory()->count(10)->create([
        'is_confidential' => false,
    ]);

    $tasks = Task::query()->get();

    expect($tasks)->toHaveCount(20);

    expect($tasks->pluck('id'))
        ->toContain(...$publicTasks->pluck('id'))
        ->toContain(...$privateTasks->pluck('id'));
});

test('users can access confidential tasks if they are the creator of the project', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user);

    $project = Project::factory()->create();
    $project->createdBy()->attach($user);

    $accessibleConfidentialTasks = Task::factory()
        ->hasAttached($project, [], 'confidentialAccessProjects')
        ->count(5)
        ->create([
            'is_confidential' => true,
        ]);

    $privateTasks = Task::factory()->count(5)->create([
        'is_confidential' => true,
    ]);

    $publicTasks = Task::factory()->count(5)->create([
        'is_confidential' => false,
    ]);

    $tasks = Task::query()->get();

    expect($tasks)->toHaveCount(10);
    expect($tasks->pluck('id'))
        ->toContain(...$publicTasks->pluck('id'))
        ->toContain(...$accessibleConfidentialTasks->pluck('id'));
    expect($tasks->pluck('id'))->not->toContain(...$privateTasks->pluck('id'));
});


test('users can access confidential tasks if they are a project manager user', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user);

    $project = Project::factory()->create();
    $project->managerUsers()->attach($user);

    $accessibleConfidentialTasks = Task::factory()
        ->hasAttached($project, [], 'confidentialAccessProjects')
        ->count(10)
        ->create([
            'is_confidential' => true,
        ]);

    $privateTasks = Task::factory()->count(10)->create([
        'is_confidential' => true,
    ]);

    $publicTasks = Task::factory()->count(10)->create([
        'is_confidential' => false,
    ]);

    $tasks = Task::query()->get();

    expect($tasks)->toHaveCount(20);

    expect($tasks->pluck('id'))
        ->toContain(...$publicTasks->pluck('id'))
        ->toContain(...$accessibleConfidentialTasks->pluck('id'));

    expect($tasks->pluck('id'))->not->toContain(...$privateTasks->pluck('id'));
});

test('users can access confidential tasks if their team is a project manager team', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();
    $team = Team::factory()->create();
    $user->team()->associate($team)->save();

    actingAs($user);

    $project = Project::factory()->create();
    $project->managerTeams()->attach($team);

    $accessibleConfidentialTasks = Task::factory()
        ->hasAttached($project, [], 'confidentialAccessProjects')
        ->count(10)
        ->create([
            'is_confidential' => true,
        ]);

    $privateTasks = Task::factory()->count(10)->create([
        'is_confidential' => true,
    ]);

    $publicTasks = Task::factory()->count(10)->create([
        'is_confidential' => false,
    ]);

    $tasks = Task::query()->get();

    expect($tasks)->toHaveCount(20);

    expect($tasks->pluck('id'))
        ->toContain(...$publicTasks->pluck('id'))
        ->toContain(...$accessibleConfidentialTasks->pluck('id'));

    expect($tasks->pluck('id'))->not->toContain(...$privateTasks->pluck('id'));
});

test('users can access confidential tasks if they are a project auditor user', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user);

    $project = Project::factory()->create();
    $project->auditorUsers()->attach($user);

    $accessibleConfidentialTasks = Task::factory()
        ->hasAttached($project, [], 'confidentialAccessProjects')
        ->count(10)
        ->create([
            'is_confidential' => true,
        ]);

    $privateTasks = Task::factory()->count(10)->create([
        'is_confidential' => true,
    ]);

    $publicTasks = Task::factory()->count(10)->create([
        'is_confidential' => false,
    ]);

    $tasks = Task::query()->get();

    expect($tasks)->toHaveCount(20);

    expect($tasks->pluck('id'))
        ->toContain(...$publicTasks->pluck('id'))
        ->toContain(...$accessibleConfidentialTasks->pluck('id'));

    expect($tasks->pluck('id'))->not->toContain(...$privateTasks->pluck('id'));
});

test('users can access confidential tasks if their team is a project auditor team', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();
    $team = Team::factory()->create();
    $user->team()->associate($team)->save();

    actingAs($user);

    $project = Project::factory()->create();
    $project->auditorTeams()->attach($team);

    $accessibleConfidentialTasks = Task::factory()
        ->hasAttached($project, [], 'confidentialAccessProjects')
        ->count(10)
        ->create([
            'is_confidential' => true,
        ]);

    $privateTasks = Task::factory()->count(10)->create([
        'is_confidential' => true,
    ]);

    $publicTasks = Task::factory()->count(10)->create([
        'is_confidential' => false,
    ]);

    $tasks = Task::query()->get();

    expect($tasks)->toHaveCount(20);

    expect($tasks->pluck('id'))
        ->toContain(...$publicTasks->pluck('id'))
        ->toContain(...$accessibleConfidentialTasks->pluck('id'));

    expect($tasks->pluck('id'))->not->toContain(...$privateTasks->pluck('id'));
});
