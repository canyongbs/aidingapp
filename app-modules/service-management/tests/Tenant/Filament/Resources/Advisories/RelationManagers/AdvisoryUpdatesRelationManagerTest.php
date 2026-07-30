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

use AidingApp\ServiceManagement\Enums\SystemAdvisoryStatusClassification;
use AidingApp\ServiceManagement\Filament\Resources\Advisories\Pages\ManageAdvisoryUpdate;
use AidingApp\ServiceManagement\Filament\Resources\Advisories\RelationManagers\AdvisoryUpdatesRelationManager;
use AidingApp\ServiceManagement\Models\Advisory;
use AidingApp\ServiceManagement\Models\AdvisoryStatus;
use AidingApp\ServiceManagement\Models\AdvisoryUpdate;
use App\Models\User;
use App\Settings\LicenseSettings;
use Filament\Actions\Testing\TestAction;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

beforeEach(function () {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = true;
    $settings->data->addons->advisoryManagement = true;
    $settings->save();
});

test('the records are displayed on the AdvisoryUpdatesRelationManager', function () {
    $advisory = Advisory::factory()->create();

    $advisoryUpdates = AdvisoryUpdate::factory()
        ->for($advisory, 'advisory')
        ->count(10)
        ->create();

    asSuperAdmin();

    livewire(AdvisoryUpdatesRelationManager::class, [
        'ownerRecord' => $advisory,
        'pageClass' => ManageAdvisoryUpdate::class,
    ])
        ->assertSuccessful()
        ->assertCanSeeTableRecords($advisoryUpdates)
        ->assertCountTableRecords(10);
});

test('only shows the updates bulk delete action to a user with the advisory_update delete permission', function () {
    $user = User::factory()
        ->create()
        ->givePermissionTo('advisory.view-any', 'advisory.*.view', 'advisory_update.view-any');

    actingAs($user);

    $advisory = Advisory::factory()->create();

    AdvisoryUpdate::factory()->for($advisory, 'advisory')->count(3)->create();

    livewire(AdvisoryUpdatesRelationManager::class, [
        'ownerRecord' => $advisory,
        'pageClass' => ManageAdvisoryUpdate::class,
    ])
        ->assertActionHidden(TestAction::make('delete')->table()->bulk());

    $user->givePermissionTo('advisory_update.*.delete');

    livewire(AdvisoryUpdatesRelationManager::class, [
        'ownerRecord' => $advisory,
        'pageClass' => ManageAdvisoryUpdate::class,
    ])
        ->assertActionVisible(TestAction::make('delete')->table()->bulk());
});

test('the create action is hidden when the advisory is resolved', function () {
    $advisory = Advisory::factory()->create([
        'status_id' => AdvisoryStatus::factory()->create([
            'classification' => SystemAdvisoryStatusClassification::Resolved,
        ])->getKey(),
    ]);

    asSuperAdmin();

    livewire(AdvisoryUpdatesRelationManager::class, [
        'ownerRecord' => $advisory,
        'pageClass' => ManageAdvisoryUpdate::class,
    ])
        ->assertTableActionHidden('create');
});

test('the create action is visible when the advisory is open', function () {
    $advisory = Advisory::factory()->create([
        'status_id' => AdvisoryStatus::factory()->create([
            'classification' => SystemAdvisoryStatusClassification::Open,
        ])->getKey(),
    ]);

    asSuperAdmin();

    livewire(AdvisoryUpdatesRelationManager::class, [
        'ownerRecord' => $advisory,
        'pageClass' => ManageAdvisoryUpdate::class,
    ])
        ->assertTableActionVisible('create');
});

test('creating an advisory update updates the advisory status', function () {
    $advisory = Advisory::factory()->create([
        'status_id' => AdvisoryStatus::factory()->create([
            'classification' => SystemAdvisoryStatusClassification::Open,
        ])->getKey(),
    ]);

    $newStatus = AdvisoryStatus::factory()->create([
        'classification' => SystemAdvisoryStatusClassification::Open,
    ]);

    asSuperAdmin();

    livewire(AdvisoryUpdatesRelationManager::class, [
        'ownerRecord' => $advisory,
        'pageClass' => ManageAdvisoryUpdate::class,
    ])
        ->callTableAction('create', data: [
            'update' => 'A new update',
            'internal' => false,
            'status_id' => $newStatus->getKey(),
        ])
        ->assertHasNoTableActionErrors();

    expect($advisory->fresh()->status_id)->toBe($newStatus->getKey());

    expect(AdvisoryUpdate::query()->where('advisory_id', $advisory->getKey())->count())->toBe(1);
});

// Permission Tests

test('AdvisoryUpdatesRelationManager create action is gated with proper access control', function () {
    $user = User::factory()->create();

    $advisory = Advisory::factory()->create([
        'status_id' => AdvisoryStatus::factory()->create([
            'classification' => SystemAdvisoryStatusClassification::Open,
        ])->getKey(),
    ]);

    $user->givePermissionTo('advisory.view-any', 'advisory.*.view', 'advisory_update.view-any');

    actingAs($user);

    livewire(AdvisoryUpdatesRelationManager::class, [
        'ownerRecord' => $advisory,
        'pageClass' => ManageAdvisoryUpdate::class,
    ])
        ->assertTableActionHidden('create');

    $user->givePermissionTo('advisory_update.create');

    livewire(AdvisoryUpdatesRelationManager::class, [
        'ownerRecord' => $advisory,
        'pageClass' => ManageAdvisoryUpdate::class,
    ])
        ->assertTableActionVisible('create');
});
