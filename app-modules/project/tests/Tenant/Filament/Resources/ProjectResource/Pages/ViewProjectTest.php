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

use AidingApp\Department\Models\Department;
use AidingApp\Project\Filament\Resources\Projects\Pages\ManageManagers;
use AidingApp\Project\Filament\Resources\Projects\Pages\ViewProject;
use AidingApp\Project\Filament\Resources\Projects\RelationManagers\ManagerUsersRelationManager;
use AidingApp\Project\Filament\Resources\Projects\Widgets\ProjectAccessWidget;
use AidingApp\Project\Filament\Resources\Projects\Widgets\ProjectMilestonesWidget;
use AidingApp\Project\Models\Project;
use AidingApp\Project\Models\ProjectMilestone;
use App\Models\User;
use Olympus\Crm\Models\Contact;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('cannot render without proper permission.', function () {
    $user = User::factory()->create();

    actingAs($user);

    $project = Project::factory()->create();

    get(ViewProject::getUrl([
        'record' => $project->getRouteKey(),
    ]))
        ->assertForbidden();
});

it('can render with proper permission.', function () {
    $user = User::factory()->create();

    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.view');

    $user->refresh();

    actingAs($user);

    $project = Project::factory()->create();

    get(ViewProject::getUrl([
        'record' => $project->getRouteKey(),
    ]))
        ->assertSuccessful();
});

it('can render if logged in user is a superadmin, the creator, a manager, or an auditor of the project.', function () {
    $user = User::factory()->create();
    $secondUser = User::factory()->create();

    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.view');

    $user->refresh();

    actingAs($user);

    $project = Project::factory()->create();

    get(ViewProject::getUrl([
        'record' => $project->getRouteKey(),
    ]))
        ->assertSuccessful();

    actingAs($secondUser);

    $secondUser->givePermissionTo('project.view-any');
    $secondUser->givePermissionTo('project.*.view');

    get(ViewProject::getUrl([
        'record' => $project->getRouteKey(),
    ]))
        ->assertNotFound();

    $project->managerUsers()->attach($secondUser->getKey());

    get(ViewProject::getUrl([
        'record' => $project->getRouteKey(),
    ]))
        ->assertSuccessful();

    $project->managerUsers()->detach($secondUser->getKey());

    get(ViewProject::getUrl([
        'record' => $project->getRouteKey(),
    ]))
        ->assertNotFound();

    $project->auditorUsers()->attach($secondUser->getKey());

    get(ViewProject::getUrl([
        'record' => $project->getRouteKey(),
    ]))
        ->assertSuccessful();

    $project->auditorUsers()->detach($secondUser->getKey());

    get(ViewProject::getUrl([
        'record' => $project->getRouteKey(),
    ]))
        ->assertNotFound();

    $department = Department::factory()->create();

    $secondUser->department()->associate($department)->save();

    $project->managerDepartments()->attach($department->getKey());

    get(ViewProject::getUrl([
        'record' => $project->getRouteKey(),
    ]))
        ->assertSuccessful();

    $project->managerDepartments()->detach($department->getKey());

    get(ViewProject::getUrl([
        'record' => $project->getRouteKey(),
    ]))
        ->assertNotFound();

    $project->auditorDepartments()->attach($department->getKey());

    get(ViewProject::getUrl([
        'record' => $project->getRouteKey(),
    ]))
        ->assertSuccessful();

    asSuperAdmin();

    get(ViewProject::getUrl([
        'record' => $project->getRouteKey(),
    ]))
        ->assertSuccessful();
});

it('can view a record', function () {
    $user = User::factory()->create();

    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.view');

    actingAs($user);

    $project = Project::factory()->create();

    livewire(ViewProject::class, [
        'record' => $project->getRouteKey(),
    ])
        ->assertHasNoErrors();
});

it('can render the project access widget and mount the manage access action', function () {
    $user = User::factory()->create();

    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.view');

    actingAs($user);

    $project = Project::factory()->create();

    livewire(ProjectAccessWidget::class, [
        'record' => $project,
    ])
        ->assertActionExists('manageAccess')
        ->mountAction('manageAccess')
        ->assertHasNoErrors();
});

it('can attach a manager user through the centralized access relation manager', function () {
    asSuperAdmin();

    $project = Project::factory()->create();

    $manager = User::factory()->create();

    livewire(ManagerUsersRelationManager::class, [
        'ownerRecord' => $project,
        'pageClass' => ManageManagers::class,
    ])
        ->callTableAction('attach', data: [
            'recordId' => $manager->getKey(),
        ])
        ->assertHasNoTableActionErrors();

    expect($project->managerUsers()->whereKey($manager->getKey())->exists())->toBeTrue();
});

it('can list milestones in the project milestones widget', function () {
    asSuperAdmin();

    $project = Project::factory()->create();

    $milestones = ProjectMilestone::factory()->count(3)->for($project)->create();

    livewire(ProjectMilestonesWidget::class, [
        'record' => $project,
    ])
        ->assertCanSeeTableRecords($milestones);
});

it('can create a milestone through the project milestones widget create action', function () {
    asSuperAdmin();

    $project = Project::factory()->create();

    $milestone = ProjectMilestone::factory()->for($project)->make();

    livewire(ProjectMilestonesWidget::class, [
        'record' => $project,
    ])
        ->callAction('manageMilestoneCreate', data: [
            'title' => $milestone->title,
            'description' => $milestone->description,
            'status_id' => $milestone->status_id,
            'target_date' => $milestone->target_date,
        ])
        ->assertHasNoActionErrors();

    expect($project->milestones()->where('title', $milestone->title)->exists())->toBeTrue();
});
