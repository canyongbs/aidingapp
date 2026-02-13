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

use AidingApp\Project\Filament\Resources\ProjectResource\Pages\ListProjects;
use AidingApp\Project\Models\Project;
use AidingApp\Team\Models\Team;
use App\Models\User;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('cannot render without proper permission.', function () {
    $user = User::factory()->create();

    actingAs($user);

    get(ListProjects::getUrl())
        ->assertForbidden();
});

it('can render with proper permission.', function () {
    $user = User::factory()->create();

    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.view');

    $user->refresh();

    actingAs($user);

    get(ListProjects::getUrl())
        ->assertSuccessful();
});

it('is gated with proper access control', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->projectManagement = false;
    $settings->save();

    $user = User::factory()->create();

    $user->givePermissionTo('project.view-any');

    actingAs($user);

    get(ListProjects::getUrl())->assertForbidden();

    $settings->data->addons->projectManagement = true;
    $settings->save();

    $user->revokePermissionTo('project.view-any');

    get(ListProjects::getUrl())->assertForbidden();

    $user->givePermissionTo('project.view-any');

    get(ListProjects::getUrl())->assertSuccessful();
});

it('can list records', function () {
    $user = User::factory()->create();

    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.view');

    actingAs($user);

    $records = Project::factory()->count(5)->create();

    livewire(ListProjects::class)
        ->assertCountTableRecords(5)
        ->assertCanSeeTableRecords($records)
        ->assertSuccessful();
});

it('can see project in list if logged in user is a superadmin, the creator, a manager, or an auditor of the project.', function () {
    $user = User::factory()->create();
    $secondUser = User::factory()->create();

    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.view');

    $user->refresh();

    actingAs($user);

    $project = Project::factory()->create();

    livewire(ListProjects::class)
        ->assertCanSeeTableRecords([$project])
        ->assertSuccessful();

    actingAs($secondUser);

    $secondUser->givePermissionTo('project.view-any');
    $secondUser->givePermissionTo('project.*.view');

    livewire(ListProjects::class)
        ->assertCanNotSeeTableRecords([$project])
        ->assertSuccessful();

    $project->managerUsers()->attach($secondUser->getKey());

    livewire(ListProjects::class)
        ->assertCanSeeTableRecords([$project])
        ->assertSuccessful();

    $project->managerUsers()->detach($secondUser->getKey());

    livewire(ListProjects::class)
        ->assertCanNotSeeTableRecords([$project])
        ->assertSuccessful();

    $project->auditorUsers()->attach($secondUser->getKey());

    livewire(ListProjects::class)
        ->assertCanSeeTableRecords([$project])
        ->assertSuccessful();

    $project->auditorUsers()->detach($secondUser->getKey());

    livewire(ListProjects::class)
        ->assertCanNotSeeTableRecords([$project])
        ->assertSuccessful();

    $team = Team::factory()->create();

    $secondUser->team()->associate($team)->save();

    $project->managerTeams()->attach($team->getKey());

    livewire(ListProjects::class)
        ->assertCanSeeTableRecords([$project])
        ->assertSuccessful();

    $project->managerTeams()->detach($team->getKey());

    livewire(ListProjects::class)
        ->assertCanNotSeeTableRecords([$project])
        ->assertSuccessful();

    $project->auditorTeams()->attach($team->getKey());

    livewire(ListProjects::class)
        ->assertCanSeeTableRecords([$project])
        ->assertSuccessful();

    asSuperAdmin();

    livewire(ListProjects::class)
        ->assertCanSeeTableRecords([$project])
        ->assertSuccessful();
});
