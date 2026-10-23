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
use AidingApp\Project\Filament\Resources\Projects\Widgets\ProjectDashboardHeaderWidget;
use AidingApp\Project\Models\Project;
use AidingApp\Project\Tests\Tenant\Filament\Resources\ProjectResource\RequestFactory\EditProjectRequestFactory;
use App\Models\User;
use App\Settings\LicenseSettings;
use Filament\Actions\Action;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

use Spatie\Permission\PermissionRegistrar;

use function Tests\asSuperAdmin;

beforeEach(function (): void {
    asSuperAdmin(User::factory()->create());
});

it('opens the edit form in a slide-over organized by headings', function (): void {
    $project = Project::factory()->create();

    Livewire::test(ProjectDashboardHeaderWidget::class, ['record' => $project])
        ->assertActionExists('editProject', fn (Action $action): bool => $action->getUrl() === null)
        ->mountAction('editProject')
        ->assertActionMounted('editProject')
        ->assertNoRedirect()
        ->assertSeeInOrder(['Properties', 'Iconography', 'Details']);
});

it('fills the form with the project data', function (): void {
    $department = Department::factory()->create();

    $project = Project::factory()->for($department)->create([
        'target_completion_date' => '2026-12-31',
    ]);

    Livewire::test(ProjectDashboardHeaderWidget::class, ['record' => $project])
        ->mountAction('editProject')
        ->assertActionDataSet([
            'name' => $project->name,
            'description' => $project->description,
            'department_id' => $department->getKey(),
            'icon' => $project->icon,
            'start_date' => $project->start_date?->toDateString(),
            'target_completion_date_type' => 'set',
            'target_completion_date' => '2026-12-31',
        ]);
});

it('defaults the target completion date to indefinite when the project has none', function (): void {
    $project = Project::factory()->create(['target_completion_date' => null]);

    Livewire::test(ProjectDashboardHeaderWidget::class, ['record' => $project])
        ->mountAction('editProject')
        ->assertActionDataSet(['target_completion_date_type' => 'indefinite']);
});

it('can update a project', function (): void {
    $project = Project::factory()->create();
    $request = EditProjectRequestFactory::new()->withTargetCompletionDate()->create();

    assertDatabaseMissing(Project::class, ['id' => $project->getKey(), 'name' => $request['name']]);

    Livewire::test(ProjectDashboardHeaderWidget::class, ['record' => $project])
        ->callAction('editProject', $request)
        ->assertDispatched('projectUpdated')
        ->assertNotified();

    assertDatabaseHas(Project::class, [
        'id' => $project->getKey(),
        'name' => $request['name'],
        'description' => $request['description'],
        'icon' => $request['icon'],
        'color' => $request['color'],
        'department_id' => $request['department_id'],
        'start_date' => $request['start_date'],
        'target_completion_date' => $request['target_completion_date'],
    ]);
});

it('clears the target completion date when switched to indefinite', function (): void {
    $project = Project::factory()->create(['target_completion_date' => '2026-12-31']);

    Livewire::test(ProjectDashboardHeaderWidget::class, ['record' => $project])
        ->mountAction('editProject')
        ->setActionData(['target_completion_date_type' => 'indefinite'])
        ->callMountedAction()
        ->assertNotified();

    expect($project->refresh()->target_completion_date)->toBeNull();
});

it('allows saving with the same name as the current project', function (): void {
    $project = Project::factory()->create();

    Livewire::test(ProjectDashboardHeaderWidget::class, ['record' => $project])
        ->mountAction('editProject')
        ->setActionData(['description' => 'Changed description'])
        ->callMountedAction()
        ->assertNotified();

    expect($project->refresh()->description)->toBe('Changed description');
});

it('validates the inputs', function (EditProjectRequestFactory $data, array $errors): void {
    $project = Project::factory()->create();

    Project::factory()->create(['name' => 'Test Project']);

    $request = EditProjectRequestFactory::new($data)->create();

    Livewire::test(ProjectDashboardHeaderWidget::class, ['record' => $project])
        ->callAction('editProject', $request)
        ->assertHasActionErrors($errors);

    assertDatabaseMissing(Project::class, ['id' => $project->getKey(), 'name' => $request['name']]);
})->with([
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
]);

describe('authorization', function (): void {
    it('hides the edit action from a user without the `project.*.update` permission', function (): void {
        $user = User::factory()->create()->givePermissionTo('project.view-any', 'project.*.view');

        actingAs($user);

        $project = Project::factory()->for($user, 'createdBy')->create();

        Livewire::test(ProjectDashboardHeaderWidget::class, ['record' => $project])
            ->assertActionHidden('editProject')
            ->mountAction('editProject')
            ->assertActionNotMounted();
    });

    it('shows the edit action to the creator of the project with the `project.*.update` permission', function (): void {
        $user = User::factory()->create()->givePermissionTo('project.view-any', 'project.*.view', 'project.*.update');

        actingAs($user);

        $project = Project::factory()->for($user, 'createdBy')->create();

        Livewire::test(ProjectDashboardHeaderWidget::class, ['record' => $project])
            ->assertActionVisible('editProject');
    });

    it('shows the edit action to a manager of the project with the `project.*.update` permission', function (): void {
        $user = User::factory()->create()->givePermissionTo('project.view-any', 'project.*.view', 'project.*.update');

        actingAs($user);

        $project = Project::factory()->hasAttached($user, relationship: 'managerUsers')->create();

        Livewire::test(ProjectDashboardHeaderWidget::class, ['record' => $project])
            ->assertActionVisible('editProject');
    });

    it('shows the edit action to a member of a managing department with the `project.*.update` permission', function (): void {
        $department = Department::factory()->create();
        $user = User::factory()->for($department)->create()->givePermissionTo('project.view-any', 'project.*.view', 'project.*.update');

        actingAs($user);

        $project = Project::factory()->hasAttached($department, relationship: 'managerDepartments')->create();

        Livewire::test(ProjectDashboardHeaderWidget::class, ['record' => $project])
            ->assertActionVisible('editProject');
    });

    it('hides the edit action from an auditor or an unrelated user even with the `project.*.update` permission', function (): void {
        $auditor = User::factory()->create()->givePermissionTo('project.view-any', 'project.*.view', 'project.*.update');
        $unrelated = User::factory()->create()->givePermissionTo('project.view-any', 'project.*.view', 'project.*.update');

        $project = Project::factory()->hasAttached($auditor, relationship: 'auditorUsers')->create();

        actingAs($auditor);

        Livewire::test(ProjectDashboardHeaderWidget::class, ['record' => $project])
            ->assertActionHidden('editProject');

        actingAs($unrelated);

        Livewire::test(ProjectDashboardHeaderWidget::class, ['record' => $project])
            ->assertActionHidden('editProject');
    });

    it('does not save for an auditor or an unrelated user even with the `project.*.update` permission', function (): void {
        $auditor = User::factory()->create()->givePermissionTo('project.view-any', 'project.*.view', 'project.*.update');
        $unrelated = User::factory()->create()->givePermissionTo('project.view-any', 'project.*.view', 'project.*.update');

        $project = Project::factory()->hasAttached($auditor, relationship: 'auditorUsers')->create();
        $request = EditProjectRequestFactory::new()->create();

        foreach ([$auditor, $unrelated] as $user) {
            actingAs($user);

            Livewire::test(ProjectDashboardHeaderWidget::class, ['record' => $project])
                ->mountAction('editProject')
                ->assertActionNotMounted()
                ->callMountedAction();
        }

        assertDatabaseMissing(Project::class, ['id' => $project->getKey(), 'name' => $request['name']]);
    });

    it('does not allow the project record to be swapped from the client', function (): void {
        $project = Project::factory()->create();
        $otherProject = Project::factory()->create();

        Livewire::test(ProjectDashboardHeaderWidget::class, ['record' => $project])
            ->set('record', $otherProject);
    })->throws('Cannot update locked property: [record]');

    it('shows the edit action to a super admin', function (): void {
        $project = Project::factory()->create();

        Livewire::test(ProjectDashboardHeaderWidget::class, ['record' => $project])
            ->assertActionVisible('editProject');
    });

    it('hides the edit action when the license addon is disabled', function (): void {
        $project = Project::factory()->create();

        $settings = app(LicenseSettings::class);
        $settings->data->addons->projectManagement = false;
        $settings->save();

        Livewire::test(ProjectDashboardHeaderWidget::class, ['record' => $project])
            ->assertActionHidden('editProject')
            ->mountAction('editProject')
            ->assertActionNotMounted();
    });

    it('does not save when the license addon is disabled after opening the slide-over', function (): void {
        $project = Project::factory()->create();
        $request = EditProjectRequestFactory::new()->create();

        $component = Livewire::test(ProjectDashboardHeaderWidget::class, ['record' => $project])
            ->mountAction('editProject')
            ->assertActionMounted('editProject');

        $settings = app(LicenseSettings::class);
        $settings->data->addons->projectManagement = false;
        $settings->save();

        $component
            ->setActionData($request)
            ->callMountedAction();

        assertDatabaseMissing(Project::class, ['id' => $project->getKey(), 'name' => $request['name']]);
    });

    it('does not save when the user loses the `project.*.update` permission after opening the slide-over', function (): void {
        $user = User::factory()->create()->givePermissionTo('project.view-any', 'project.*.view', 'project.*.update');

        actingAs($user);

        $project = Project::factory()->for($user, 'createdBy')->create();
        $request = EditProjectRequestFactory::new()->create();

        $component = Livewire::test(ProjectDashboardHeaderWidget::class, ['record' => $project])
            ->mountAction('editProject')
            ->assertActionMounted('editProject');

        $user->revokePermissionTo('project.*.update');
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $component
            ->setActionData($request)
            ->callMountedAction();

        assertDatabaseMissing(Project::class, ['id' => $project->getKey(), 'name' => $request['name']]);
    });
});
