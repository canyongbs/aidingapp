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
use AidingApp\Project\Filament\Resources\ProjectResource\Pages\ManageTasks;
use AidingApp\Project\Models\Project;
use AidingApp\Task\Models\Task;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\AssociateAction;
use Filament\Tables\Actions\DissociateAction;
use Filament\Tables\Actions\DissociateBulkAction;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('cannot render without proper permission.', function () {
    $user = User::factory()->create();

    actingAs($user);

    $project = Project::factory()->create();

    get(ManageTasks::getUrl([
        'record' => $project->getRouteKey(),
    ]))
        ->assertForbidden();
});

it('can render with proper permission.', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.view');

    $user->refresh();

    actingAs($user);

    $project = Project::factory()->create();

    get(ManageTasks::getUrl([
        'record' => $project->getRouteKey(),
    ]))
        ->assertForbidden();

    $user->revokePermissionTo('project.view-any');
    $user->revokePermissionTo('project.*.view');
    $user->givePermissionTo('task.view-any');
    $user->givePermissionTo('task.*.view');

    $user->refresh();

    get(ManageTasks::getUrl([
        'record' => $project->getRouteKey(),
    ]))
        ->assertForbidden();

    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.view');

    $user->refresh();

    get(ManageTasks::getUrl([
        'record' => $project->getRouteKey(),
    ]))
        ->assertSuccessful();
});

it('can visible associate action with proper permission.', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.view');
    $user->givePermissionTo('task.view-any');
    $user->givePermissionTo('task.*.view');

    $user->refresh();

    actingAs($user);

    $project = Project::factory()->create();

    livewire(ManageTasks::class, [
        'record' => $project->getRouteKey(),
    ])
        ->assertTableActionHidden(AssociateAction::class);

    $user->givePermissionTo('task.*.update');

    livewire(ManageTasks::class, [
        'record' => $project->getRouteKey(),
    ])
        ->assertTableActionVisible(AssociateAction::class);
});

it('can visible dissociate action with proper permission.', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.view');
    $user->givePermissionTo('task.view-any');
    $user->givePermissionTo('task.*.view');

    $user->refresh();

    actingAs($user);

    $project = Project::factory()->create();

    Task::factory()->count(5)->for($project)->create();

    livewire(ManageTasks::class, [
        'record' => $project->getRouteKey(),
    ])
        ->assertTableActionHidden(DissociateAction::class)
        ->assertTableBulkActionHidden(DissociateBulkAction::class);

    $user->givePermissionTo('task.*.update');

    livewire(ManageTasks::class, [
        'record' => $project->getRouteKey(),
    ])
        ->assertTableActionVisible(DissociateAction::class)
        ->assertTableBulkActionVisible(DissociateBulkAction::class);
});

it('can list tasks', function () {
    asSuperAdmin();

    $project = Project::factory()->create();

    Task::factory()->count(5)->for($project)->create();

    livewire(ManageTasks::class, [
        'record' => $project->getRouteKey(),
    ])
        ->assertCanSeeTableRecords($project->tasks);
});

it('does not list tasks already associated with a project in task search results', function () {
    asSuperAdmin();

    $project = Project::factory()->create();

    $secondProject = Project::factory()->create();

    $task = Task::factory()->create();

    livewire(ManageTasks::class, [
        'record' => $project->getRouteKey(),
    ])
        ->mountTableAction(AssociateAction::class)
        ->assertFormFieldExists('recordId', 'mountedTableActionForm', function (Select $select) use ($task) {
            $options = $select->getSearchResults($task->title);

            return ! empty($options);
        })->assertSuccessful();

    $task->project()->associate($project)->save();

    livewire(ManageTasks::class, [
        'record' => $project->getRouteKey(),
    ])
        ->mountTableAction(AssociateAction::class)
        ->assertFormFieldExists('recordId', 'mountedTableActionForm', function (Select $select) use ($task) {
            $options = $select->getSearchResults($task->title);

            return empty($options);
        })->assertSuccessful();
});
