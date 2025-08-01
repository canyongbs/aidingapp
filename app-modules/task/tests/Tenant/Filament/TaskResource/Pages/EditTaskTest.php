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

use AidingApp\Authorization\Enums\LicenseType;
use AidingApp\Task\Filament\Resources\TaskResource;
use AidingApp\Task\Filament\Resources\TaskResource\Pages\EditTask;
use AidingApp\Task\Models\Task;
use AidingApp\Task\Tests\Tenant\RequestFactories\EditTaskRequestFactory;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

// TODO: Write EditTask page tests
//test('A successful action on the EditTask page', function () {});
//
//test('EditTask requires valid data', function ($data, $errors) {})->with([]);

// Permission Tests

test('EditTask is gated with proper access control', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $task = Task::factory()->create();

    actingAs($user)
        ->get(
            TaskResource::getUrl('edit', [
                'record' => $task,
            ])
        )->assertForbidden();

    livewire(EditTask::class, [
        'record' => $task->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('task.view-any');
    $user->givePermissionTo('task.*.update');

    actingAs($user)
        ->get(
            TaskResource::getUrl('edit', [
                'record' => $task,
            ])
        )->assertForbidden();

    $task->assignedTo()->associate($user)->save();

    $task->refresh();

    actingAs($user)
        ->get(
            TaskResource::getUrl('edit', [
                'record' => $task,
            ])
        )->assertSuccessful();

    // TODO: Finish these tests to ensure changes are allowed
    $request = collect(EditTaskRequestFactory::new()->create());

    livewire(EditTask::class, [
        'record' => $task->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    // TODO: Check for changes
});

test('A user without proper permissions and that is not associated to the Task cannot access', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $task = Task::factory()->create();

    actingAs($user)
        ->get(
            TaskResource::getUrl('edit', [
                'record' => $task,
            ])
        )->assertForbidden();

    livewire(EditTask::class, [
        'record' => $task->getRouteKey(),
    ])
        ->assertForbidden();
});

test('A User with proper permissions and that is not associated to the Task cannot access', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $task = Task::factory()->create();

    $user->givePermissionTo('task.view-any');
    $user->givePermissionTo('task.*.update');

    actingAs($user)
        ->get(
            TaskResource::getUrl('edit', [
                'record' => $task,
            ])
        )->assertForbidden();

    livewire(EditTask::class, [
        'record' => $task->getRouteKey(),
    ])
        ->assertForbidden();
});

test('A User without proper permissions that is the assigned user cannot access', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $task = Task::factory()->create();

    $task->assignedTo()->associate($user)->save();

    $task->refresh();

    actingAs($user)
        ->get(
            TaskResource::getUrl('edit', [
                'record' => $task,
            ])
        )->assertForbidden();

    livewire(EditTask::class, [
        'record' => $task->getRouteKey(),
    ])
        ->assertForbidden();
});

test('A User without proper permissions that is the created by user cannot access', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $task = Task::factory()->create();

    $task->createdBy()->associate($user)->save();

    $task->refresh();

    actingAs($user)
        ->get(
            TaskResource::getUrl('edit', [
                'record' => $task,
            ])
        )->assertForbidden();

    livewire(EditTask::class, [
        'record' => $task->getRouteKey(),
    ])
        ->assertForbidden();
});

test('A User with proper permissions that is the assigned user can access', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $task = Task::factory()->create();

    $user->givePermissionTo('task.view-any');
    $user->givePermissionTo('task.*.update');

    $task->assignedTo()->associate($user)->save();

    $task->refresh();

    actingAs($user)
        ->get(
            TaskResource::getUrl('edit', [
                'record' => $task,
            ])
        )->assertSuccessful();

    livewire(EditTask::class, [
        'record' => $task->getRouteKey(),
    ])
        ->assertSuccessful();
});

test('A User with proper permissions that is the created by user can access.', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $task = Task::factory()->create();

    $user->givePermissionTo('task.view-any');
    $user->givePermissionTo('task.*.update');

    $task->createdBy()->associate($user)->save();

    $task->refresh();

    actingAs($user)
        ->get(
            TaskResource::getUrl('edit', [
                'record' => $task,
            ])
        )->assertSuccessful();

    livewire(EditTask::class, [
        'record' => $task->getRouteKey(),
    ])
        ->assertSuccessful();
});
