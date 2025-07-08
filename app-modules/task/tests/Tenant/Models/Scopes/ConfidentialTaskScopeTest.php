<?php

use AidingApp\Authorization\Enums\LicenseType;
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
