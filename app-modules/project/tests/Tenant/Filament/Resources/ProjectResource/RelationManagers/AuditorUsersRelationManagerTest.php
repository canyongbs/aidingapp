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

use AidingApp\Project\Filament\Resources\Projects\Pages\ManageAuditors;
use AidingApp\Project\Filament\Resources\Projects\RelationManagers\AuditorUsersRelationManager;
use AidingApp\Project\Models\Project;
use App\Models\User;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\Testing\TestAction;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('can list auditor users', function () {
    asSuperAdmin();

    $project = Project::factory()->create();

    $auditors = User::factory()->count(3)->create();

    $project->auditorUsers()->attach($auditors->pluck('id'));

    livewire(AuditorUsersRelationManager::class, [
        'ownerRecord' => $project,
        'pageClass' => ManageAuditors::class,
    ])
        ->assertCanSeeTableRecords($auditors);
});

it('can attach an auditor user to a project', function () {
    asSuperAdmin();

    $project = Project::factory()->create();

    $auditor = User::factory()->create();

    livewire(AuditorUsersRelationManager::class, [
        'ownerRecord' => $project,
        'pageClass' => ManageAuditors::class,
    ])
        ->callAction(TestAction::make(AttachAction::class)->table(), data: [
            'recordId' => $auditor->getKey(),
        ])
        ->assertHasNoFormErrors();

    expect($project->auditorUsers()->whereKey($auditor->getKey())->exists())->toBeTrue();
});

it('can detach an auditor user from a project', function () {
    asSuperAdmin();

    $project = Project::factory()->create();

    $auditor = User::factory()->create();

    $project->auditorUsers()->attach($auditor->getKey());

    livewire(AuditorUsersRelationManager::class, [
        'ownerRecord' => $project,
        'pageClass' => ManageAuditors::class,
    ])
        ->callAction(TestAction::make(DetachAction::class)->table($auditor))
        ->assertHasNoFormErrors();

    expect($project->auditorUsers()->whereKey($auditor->getKey())->exists())->toBeFalse();
});

it('can bulk detach auditor users from a project', function () {
    asSuperAdmin();

    $project = Project::factory()->create();

    $auditors = User::factory()->count(2)->create();

    $project->auditorUsers()->attach($auditors->pluck('id'));

    livewire(AuditorUsersRelationManager::class, [
        'ownerRecord' => $project,
        'pageClass' => ManageAuditors::class,
    ])
        ->selectTableRecords($auditors->pluck('id')->all())
        ->callAction(TestAction::make(DetachBulkAction::class)->table()->bulk())
        ->assertHasNoFormErrors();

    expect($project->auditorUsers()->count())->toBe(0);
});

describe('authorization', function () {
    it('allows a super admin to attach an auditor user', function () {
        asSuperAdmin();

        $project = Project::factory()->create();

        livewire(AuditorUsersRelationManager::class, [
            'ownerRecord' => $project,
            'pageClass' => ManageAuditors::class,
        ])
            ->assertActionVisible(TestAction::make(AttachAction::class)->table());
    });

    it('allows the project creator with the update permission to attach an auditor user', function () {
        $user = User::factory()->create();

        $user->givePermissionTo('project.view-any');
        $user->givePermissionTo('project.*.view');
        $user->givePermissionTo('project.*.update');

        actingAs($user);

        $project = Project::factory()->for($user, 'createdBy')->create();

        $auditor = User::factory()->create();

        livewire(AuditorUsersRelationManager::class, [
            'ownerRecord' => $project,
            'pageClass' => ManageAuditors::class,
        ])
            ->assertActionVisible(TestAction::make(AttachAction::class)->table())
            ->callAction(TestAction::make(AttachAction::class)->table(), data: [
                'recordId' => $auditor->getKey(),
            ])
            ->assertHasNoFormErrors();

        expect($project->auditorUsers()->whereKey($auditor->getKey())->exists())->toBeTrue();
    });

    it('allows an assigned project manager with the update permission to attach an auditor user', function () {
        $user = User::factory()->create();

        $user->givePermissionTo('project.view-any');
        $user->givePermissionTo('project.*.view');
        $user->givePermissionTo('project.*.update');

        $project = Project::factory()->for(User::factory(), 'createdBy')->create();

        $project->managerUsers()->attach($user->getKey());

        actingAs($user);

        livewire(AuditorUsersRelationManager::class, [
            'ownerRecord' => $project,
            'pageClass' => ManageAuditors::class,
        ])
            ->assertActionVisible(TestAction::make(AttachAction::class)->table());
    });

    it('hides the attach action when the user is only an auditor, not a manager or creator', function () {
        $user = User::factory()->create();

        $user->givePermissionTo('project.view-any');
        $user->givePermissionTo('project.*.view');
        $user->givePermissionTo('project.*.update');

        $project = Project::factory()->for(User::factory(), 'createdBy')->create();

        $project->auditorUsers()->attach($user->getKey());

        actingAs($user);

        livewire(AuditorUsersRelationManager::class, [
            'ownerRecord' => $project,
            'pageClass' => ManageAuditors::class,
        ])
            ->assertActionHidden(TestAction::make(AttachAction::class)->table());
    });

    it('hides the attach action when the user has no update permission', function () {
        $user = User::factory()->create();

        $user->givePermissionTo('project.view-any');
        $user->givePermissionTo('project.*.view');

        actingAs($user);

        $project = Project::factory()->for($user, 'createdBy')->create();

        livewire(AuditorUsersRelationManager::class, [
            'ownerRecord' => $project,
            'pageClass' => ManageAuditors::class,
        ])
            ->assertActionHidden(TestAction::make(AttachAction::class)->table());
    });

    it('hides the attach action when the user has the update permission but is unrelated to the project', function () {
        $user = User::factory()->create();

        $user->givePermissionTo('project.view-any');
        $user->givePermissionTo('project.*.view');
        $user->givePermissionTo('project.*.update');

        $project = Project::factory()->for(User::factory(), 'createdBy')->create();

        actingAs($user);

        livewire(AuditorUsersRelationManager::class, [
            'ownerRecord' => $project,
            'pageClass' => ManageAuditors::class,
        ])
            ->assertActionHidden(TestAction::make(AttachAction::class)->table());
    });
});
