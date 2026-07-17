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
use AidingApp\Project\Filament\Resources\Projects\Pages\EditProject;
use AidingApp\Project\Models\Project;
use AidingApp\Project\Tests\Tenant\Filament\Resources\ProjectResource\RequestFactory\EditProjectRequestFactory;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('cannot render without proper permission.', function () {
    $user = User::factory()->create();

    actingAs($user);

    $project = Project::factory()->create();

    get(EditProject::getUrl([
        'record' => $project->getRouteKey(),
    ]))
        ->assertForbidden();
});

it('can render with proper permission.', function () {
    $user = User::factory()->create();

    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.update');

    $user->refresh();

    actingAs($user);

    $project = Project::factory()->create();

    get(EditProject::getUrl([
        'record' => $project->getRouteKey(),
    ]))
        ->assertSuccessful();
});

it('can render if logged in user is superadmin, creator, or a manager of the project.', function () {
    $user = User::factory()->create();
    $secondUser = User::factory()->create();

    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.update');

    $user->refresh();

    actingAs($user);

    $project = Project::factory()->create();

    get(EditProject::getUrl([
        'record' => $project->getRouteKey(),
    ]))
        ->assertSuccessful();

    actingAs($secondUser);

    $secondUser->givePermissionTo('project.view-any');
    $secondUser->givePermissionTo('project.*.update');

    get(EditProject::getUrl([
        'record' => $project->getRouteKey(),
    ]))
        ->assertNotFound();

    $project->managerUsers()->attach($secondUser->getKey());

    get(EditProject::getUrl([
        'record' => $project->getRouteKey(),
    ]))
        ->assertSuccessful();

    $project->managerUsers()->detach($secondUser->getKey());

    get(EditProject::getUrl([
        'record' => $project->getRouteKey(),
    ]))
        ->assertNotFound();

    $project->auditorUsers()->attach($secondUser->getKey());

    get(EditProject::getUrl([
        'record' => $project->getRouteKey(),
    ]))
        ->assertForbidden();

    $project->auditorUsers()->detach($secondUser->getKey());

    get(EditProject::getUrl([
        'record' => $project->getRouteKey(),
    ]))
        ->assertNotFound();

    $department = Department::factory()->create();

    $secondUser->department()->associate($department)->save();

    $project->managerDepartments()->attach($department->getKey());

    get(EditProject::getUrl([
        'record' => $project->getRouteKey(),
    ]))
        ->assertSuccessful();

    $project->managerDepartments()->detach($department->getKey());

    get(EditProject::getUrl([
        'record' => $project->getRouteKey(),
    ]))
        ->assertNotFound();

    $project->auditorDepartments()->attach($department->getKey());

    get(EditProject::getUrl([
        'record' => $project->getRouteKey(),
    ]))
        ->assertForbidden();

    asSuperAdmin();

    get(EditProject::getUrl([
        'record' => $project->getRouteKey(),
    ]))
        ->assertSuccessful();
});

it('validates the inputs', function (EditProjectRequestFactory $data, array $errors) {
    asSuperAdmin();

    $user = User::factory()->create();

    $project = Project::factory()->for($user, 'createdBy')->create();

    Project::factory()->for($user, 'createdBy')->create(['name' => 'Test Project']);

    $request = EditProjectRequestFactory::new($data)->create();

    livewire(EditProject::class, [
        'record' => $project->getRouteKey(),
    ])
        ->fillForm($request)
        ->call('save')
        ->assertHasFormErrors($errors);

    assertDatabaseMissing(
        Project::class,
        collect($request)->except('target_completion_date_type')->toArray()
    );
})->with(
    [
        'name required' => [
            EditProjectRequestFactory::new()->state(['name' => null]),
            ['name' => 'required'],
        ],
        'name string' => [
            EditProjectRequestFactory::new()->state(['name' => 1]),
            ['name' => 'string'],
        ],
        'name max' => [
            EditProjectRequestFactory::new()->state(['name' => str()->random(256)]),
            ['name' => 'max'],
        ],
        'name unique' => [
            EditProjectRequestFactory::new()->state(['name' => 'Test Project']),
            ['name' => 'unique'],
        ],
        'description string' => [
            EditProjectRequestFactory::new()->state(['description' => 1]),
            ['description' => 'string'],
        ],
        'description max' => [
            EditProjectRequestFactory::new()->state(['description' => str()->random(65536)]),
            ['description' => 'max'],
        ],
        'icon required' => [
            EditProjectRequestFactory::new()->state(['icon' => null]),
            ['icon' => 'required'],
        ],
        'color required' => [
            EditProjectRequestFactory::new()->state(['color' => null]),
            ['color' => 'required'],
        ],
        'color invalid' => [
            EditProjectRequestFactory::new()->state(['color' => 'not-a-color']),
            ['color' => 'in'],
        ],
    ]
);

it('can edit a record', function () {
    $user = User::factory()->create();

    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.update');

    actingAs($user);

    $project = Project::factory()->for($user, 'createdBy')->create();

    $request = EditProjectRequestFactory::new()->create();

    livewire(EditProject::class, [
        'record' => $project->getRouteKey(),
    ])
        ->fillForm($request)
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertHasNoErrors();

    assertDatabaseCount(Project::class, 1);

    assertDatabaseHas(
        Project::class,
        [
            'name' => $request['name'],
            'description' => $request['description'],
            'icon' => $request['icon'],
            'color' => $request['color'],
            'department_id' => $request['department_id'],
            'start_date' => $request['start_date'],
        ]
    );
});

it('can edit a record with target completion date', function () {
    $user = User::factory()->create();

    $user->givePermissionTo('project.view-any');
    $user->givePermissionTo('project.*.update');

    actingAs($user);

    $project = Project::factory()->for($user, 'createdBy')->create();

    $request = EditProjectRequestFactory::new()->withTargetCompletionDate()->create();

    livewire(EditProject::class, [
        'record' => $project->getRouteKey(),
    ])
        ->fillForm($request)
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertHasNoErrors();

    assertDatabaseHas(
        Project::class,
        [
            'id' => $project->getKey(),
            'target_completion_date' => $request['target_completion_date'],
        ]
    );
});

it('clears target_completion_date when switching to indefinite', function () {
    asSuperAdmin();

    $project = Project::factory()->create([
        'icon' => 'heroicon-o-clipboard-document-list',
        'target_completion_date' => '2026-12-31',
    ]);

    livewire(EditProject::class, [
        'record' => $project->getRouteKey(),
    ])
        ->assertFormSet([
            'target_completion_date_type' => 'set',
        ])
        ->set('data.target_completion_date_type', 'indefinite')
        ->set('data.target_completion_date', null)
        ->call('save')
        ->assertHasNoFormErrors();

    assertDatabaseHas(
        Project::class,
        [
            'id' => $project->getKey(),
            'target_completion_date' => null,
        ]
    );
});

it('hydrates target_completion_date_type to set when record has target_completion_date', function () {
    asSuperAdmin();

    $project = Project::factory()->create([
        'target_completion_date' => '2026-12-31',
    ]);

    livewire(EditProject::class, [
        'record' => $project->getRouteKey(),
    ])
        ->assertFormFieldExists('target_completion_date_type')
        ->assertFormFieldExists('target_completion_date');
});
