<?php

use AidingApp\Authorization\Enums\LicenseType;
use AidingApp\Task\Models\Scopes\TaskConfidentialScope;
use AidingApp\Task\Models\Task;
use AidingApp\Team\Models\Team;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Tests\asSuperAdmin;

test('Interaction model has applied global scope', function () {
    Task::bootHasGlobalScopes();

    expect(Task::hasGlobalScope(TaskConfidentialScope::class))->toBeTrue();
});

test('User Can Access Public and Self-Created Confidential Tasks', function () {
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

test('Confidential Tasks Are Visible to Team Members with Access', function () {
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

test('Assigned User Can Only Access Permitted Confidential Tasks', function () {
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

test('SuperAdmin Can Access All Tasks Including Confidential Ones', function () {
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
