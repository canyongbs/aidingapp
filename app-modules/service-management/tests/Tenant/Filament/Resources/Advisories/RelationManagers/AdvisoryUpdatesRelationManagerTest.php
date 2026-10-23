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
use AidingApp\ServiceManagement\Filament\Resources\Advisories\Pages\ViewAdvisory;
use AidingApp\ServiceManagement\Filament\Resources\Advisories\RelationManagers\AdvisoryUpdatesRelationManager;
use AidingApp\ServiceManagement\Models\Advisory;
use AidingApp\ServiceManagement\Models\AdvisoryStatus;
use AidingApp\ServiceManagement\Models\AdvisoryUpdate;
use App\Models\User;
use App\Settings\LicenseSettings;
use Filament\Actions\Testing\TestAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\ToggleButtons;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Tests\asSuperAdmin;

beforeEach(function (): void {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = true;
    $settings->data->addons->advisoryManagement = true;
    $settings->save();
});

test('the records are displayed on the AdvisoryUpdatesRelationManager', function (): void {
    $advisory = Advisory::factory()->create();

    $advisoryUpdates = AdvisoryUpdate::factory()
        ->for($advisory, 'advisory')
        ->count(10)
        ->create();

    asSuperAdmin();

    Livewire::test(AdvisoryUpdatesRelationManager::class, [
        'ownerRecord' => $advisory,
        'pageClass' => ViewAdvisory::class,
    ])
        ->assertSuccessful()
        ->assertCanSeeTableRecords($advisoryUpdates)
        ->assertCountTableRecords(10);
});

test('only shows the updates bulk delete action to a user with the advisory_update delete permission', function (): void {
    $user = User::factory()
        ->create()
        ->givePermissionTo('advisory.view-any', 'advisory.*.view', 'advisory_update.view-any');

    actingAs($user);

    $advisory = Advisory::factory()->create();

    AdvisoryUpdate::factory()->for($advisory, 'advisory')->count(3)->create();

    Livewire::test(AdvisoryUpdatesRelationManager::class, [
        'ownerRecord' => $advisory,
        'pageClass' => ViewAdvisory::class,
    ])
        ->assertActionHidden(TestAction::make('delete')->table()->bulk());

    $user->givePermissionTo('advisory_update.*.delete');

    Livewire::test(AdvisoryUpdatesRelationManager::class, [
        'ownerRecord' => $advisory,
        'pageClass' => ViewAdvisory::class,
    ])
        ->assertActionVisible(TestAction::make('delete')->table()->bulk());
});

test('the create action is hidden when the advisory is resolved', function (): void {
    $advisory = Advisory::factory()->create([
        'status_id' => AdvisoryStatus::factory()->create([
            'classification' => SystemAdvisoryStatusClassification::Resolved,
        ])->getKey(),
    ]);

    asSuperAdmin();

    Livewire::test(AdvisoryUpdatesRelationManager::class, [
        'ownerRecord' => $advisory,
        'pageClass' => ViewAdvisory::class,
    ])
        ->assertTableActionHidden('create');
});

test('the create action is visible when the advisory is open', function (): void {
    $advisory = Advisory::factory()->create([
        'status_id' => AdvisoryStatus::factory()->create([
            'classification' => SystemAdvisoryStatusClassification::Open,
        ])->getKey(),
    ]);

    asSuperAdmin();

    Livewire::test(AdvisoryUpdatesRelationManager::class, [
        'ownerRecord' => $advisory,
        'pageClass' => ViewAdvisory::class,
    ])
        ->assertTableActionVisible('create');
});

test('creating an advisory update updates the advisory status', function (): void {
    $advisory = Advisory::factory()->create([
        'status_id' => AdvisoryStatus::factory()->create([
            'classification' => SystemAdvisoryStatusClassification::Open,
        ])->getKey(),
    ]);

    $newStatus = AdvisoryStatus::factory()->create([
        'classification' => SystemAdvisoryStatusClassification::Open,
    ]);

    asSuperAdmin();

    Livewire::test(AdvisoryUpdatesRelationManager::class, [
        'ownerRecord' => $advisory,
        'pageClass' => ViewAdvisory::class,
    ])
        ->callTableAction('create', data: [
            'title' => 'A new title',
            'update' => 'A new update',
            'internal' => false,
            'status_id' => $newStatus->getKey(),
        ])
        ->assertHasNoTableActionErrors();

    expect($advisory->fresh()->status_id)->toBe($newStatus->getKey());

    expect(AdvisoryUpdate::query()->where('advisory_id', $advisory->getKey())->count())->toBe(1);
});

test('creating an advisory update that resolves the advisory hides the create action without a page refresh', function (): void {
    $advisory = Advisory::factory()->create([
        'status_id' => AdvisoryStatus::factory()->create([
            'classification' => SystemAdvisoryStatusClassification::Open,
        ])->getKey(),
    ]);

    $resolvedStatus = AdvisoryStatus::factory()->create([
        'classification' => SystemAdvisoryStatusClassification::Resolved,
    ]);

    asSuperAdmin();

    $component = Livewire::test(AdvisoryUpdatesRelationManager::class, [
        'ownerRecord' => $advisory,
        'pageClass' => ViewAdvisory::class,
    ])
        ->assertTableActionVisible('create')
        ->callTableAction('create', data: [
            'title' => 'A new title',
            'update' => 'A new update',
            'internal' => false,
            'status_id' => $resolvedStatus->getKey(),
        ])
        ->assertHasNoTableActionErrors();

    $component->assertTableActionHidden('create');
});

test('the create action opens in a slide over with the advisory status field labelled and described', function (): void {
    $advisory = Advisory::factory()->create([
        'status_id' => AdvisoryStatus::factory()->create([
            'classification' => SystemAdvisoryStatusClassification::Open,
        ])->getKey(),
    ]);

    asSuperAdmin();

    $component = Livewire::test(AdvisoryUpdatesRelationManager::class, [
        'ownerRecord' => $advisory,
        'pageClass' => ViewAdvisory::class,
    ])
        ->mountTableAction('create')
        ->assertFormFieldExists('status_id', function (ToggleButtons $field): bool {
            return $field->getLabel() === 'Advisory Status'
                && str_contains(
                    (string) $field->getChildSchema($field::ABOVE_CONTENT_SCHEMA_KEY)?->toHtmlString(),
                    'You may change the overall status of the advisory by selecting a new status below.',
                );
        });

    expect($component->instance()->getTable()->getAction('create')->isModalSlideOver())->toBeTrue();
});

test('the view or edit action opens in a slide over with the advisory status field labelled and described', function (): void {
    $advisory = Advisory::factory()->create([
        'status_id' => AdvisoryStatus::factory()->create([
            'classification' => SystemAdvisoryStatusClassification::Open,
        ])->getKey(),
    ]);

    $advisoryUpdate = AdvisoryUpdate::factory()->for($advisory, 'advisory')->create();

    asSuperAdmin();

    $component = Livewire::test(AdvisoryUpdatesRelationManager::class, [
        'ownerRecord' => $advisory,
        'pageClass' => ViewAdvisory::class,
    ])
        ->mountAction(TestAction::make('viewOrEditAdvisoryUpdate')->table($advisoryUpdate))
        ->assertFormFieldExists('status_id', function (ToggleButtons $field): bool {
            return $field->getLabel() === 'Advisory Status'
                && str_contains(
                    (string) $field->getChildSchema($field::ABOVE_CONTENT_SCHEMA_KEY)?->toHtmlString(),
                    'You may change the overall status of the advisory by selecting a new status below.',
                );
        });

    expect($component->instance()->getTable()->getAction('viewOrEditAdvisoryUpdate')->isModalSlideOver())->toBeTrue();
});

test('the view or edit action hides the status change guidance when the user cannot update the advisory update', function (): void {
    // Resolved advisories cannot be edited, per AdvisoryUpdatePolicy::update().
    $advisory = Advisory::factory()->create([
        'status_id' => AdvisoryStatus::factory()->create([
            'classification' => SystemAdvisoryStatusClassification::Resolved,
        ])->getKey(),
    ]);

    $advisoryUpdate = AdvisoryUpdate::factory()->for($advisory, 'advisory')->create();

    $user = User::factory()
        ->create()
        ->givePermissionTo('advisory.view-any', 'advisory.*.view', 'advisory_update.view-any', 'advisory_update.*.view', 'advisory_update.*.update');

    actingAs($user);

    Livewire::test(AdvisoryUpdatesRelationManager::class, [
        'ownerRecord' => $advisory,
        'pageClass' => ViewAdvisory::class,
    ])
        ->mountAction(TestAction::make('viewOrEditAdvisoryUpdate')->table($advisoryUpdate))
        ->assertFormFieldExists('status_id', function (ToggleButtons $field): bool {
            return ! str_contains(
                (string) $field->getChildSchema($field::ABOVE_CONTENT_SCHEMA_KEY)?->toHtmlString(),
                'You may change the overall status of the advisory by selecting a new status below.',
            );
        });
});

test('the create form date field defaults to now', function (): void {
    $advisory = Advisory::factory()->create([
        'status_id' => AdvisoryStatus::factory()->create([
            'classification' => SystemAdvisoryStatusClassification::Open,
        ])->getKey(),
    ]);

    asSuperAdmin();

    Livewire::test(AdvisoryUpdatesRelationManager::class, [
        'ownerRecord' => $advisory,
        'pageClass' => ViewAdvisory::class,
    ])
        ->mountTableAction('create')
        ->assertFormFieldExists('date', function (DateTimePicker $field): bool {
            $default = $field->getState();

            return $default !== null && now()->diffInSeconds($default) < 10;
        });
});

test('the title column with a description replaces the update column', function (): void {
    $advisory = Advisory::factory()->create();

    $advisoryUpdate = AdvisoryUpdate::factory()->for($advisory, 'advisory')->create();

    asSuperAdmin();

    Livewire::test(AdvisoryUpdatesRelationManager::class, [
        'ownerRecord' => $advisory,
        'pageClass' => ViewAdvisory::class,
    ])
        ->assertTableColumnDoesNotExist('update')
        ->assertTableColumnExists('title')
        ->assertTableColumnExists('date')
        ->assertTableColumnStateSet('title', $advisoryUpdate->title, record: $advisoryUpdate)
        ->assertTableColumnHasDescription('title', $advisoryUpdate->update, record: $advisoryUpdate);
});

test('clicking the title opens an editable modal when the user can update the advisory update', function (): void {
    $advisory = Advisory::factory()->create([
        'status_id' => AdvisoryStatus::factory()->create([
            'classification' => SystemAdvisoryStatusClassification::Open,
        ])->getKey(),
    ]);

    $advisoryUpdate = AdvisoryUpdate::factory()->for($advisory, 'advisory')->create();

    asSuperAdmin();

    Livewire::test(AdvisoryUpdatesRelationManager::class, [
        'ownerRecord' => $advisory,
        'pageClass' => ViewAdvisory::class,
    ])
        ->mountAction(TestAction::make('viewOrEditAdvisoryUpdate')->table($advisoryUpdate))
        ->assertActionMounted(TestAction::make('viewOrEditAdvisoryUpdate')->table($advisoryUpdate))
        ->assertFormFieldEnabled('title');
});

test('clicking the title opens a read-only modal when the user cannot update the advisory update', function (): void {
    // Resolved advisories cannot be edited, per AdvisoryUpdatePolicy::update().
    $advisory = Advisory::factory()->create([
        'status_id' => AdvisoryStatus::factory()->create([
            'classification' => SystemAdvisoryStatusClassification::Resolved,
        ])->getKey(),
    ]);

    $advisoryUpdate = AdvisoryUpdate::factory()->for($advisory, 'advisory')->create();

    $user = User::factory()
        ->create()
        ->givePermissionTo('advisory.view-any', 'advisory.*.view', 'advisory_update.view-any', 'advisory_update.*.view', 'advisory_update.*.update');

    actingAs($user);

    Livewire::test(AdvisoryUpdatesRelationManager::class, [
        'ownerRecord' => $advisory,
        'pageClass' => ViewAdvisory::class,
    ])
        ->mountAction(TestAction::make('viewOrEditAdvisoryUpdate')->table($advisoryUpdate))
        ->assertActionMounted(TestAction::make('viewOrEditAdvisoryUpdate')->table($advisoryUpdate))
        ->assertFormFieldDisabled('title');
});

test('a user cannot mount the view or edit advisory update action without the view permission', function (): void {
    $advisory = Advisory::factory()->create([
        'status_id' => AdvisoryStatus::factory()->create([
            'classification' => SystemAdvisoryStatusClassification::Open,
        ])->getKey(),
    ]);

    $advisoryUpdate = AdvisoryUpdate::factory()->for($advisory, 'advisory')->create();

    $user = User::factory()
        ->create()
        ->givePermissionTo('advisory.view-any', 'advisory.*.view', 'advisory_update.view-any');

    actingAs($user);

    Livewire::test(AdvisoryUpdatesRelationManager::class, [
        'ownerRecord' => $advisory,
        'pageClass' => ViewAdvisory::class,
    ])
        ->assertActionHidden(TestAction::make('viewOrEditAdvisoryUpdate')->table($advisoryUpdate));
});

test('submitting the view or edit advisory update action updates the advisory update and the advisory status', function (): void {
    $advisory = Advisory::factory()->create([
        'status_id' => AdvisoryStatus::factory()->create([
            'classification' => SystemAdvisoryStatusClassification::Open,
        ])->getKey(),
    ]);

    $advisoryUpdate = AdvisoryUpdate::factory()->for($advisory, 'advisory')->create();

    $newStatus = AdvisoryStatus::factory()->create([
        'classification' => SystemAdvisoryStatusClassification::Open,
    ]);

    asSuperAdmin();

    Livewire::test(AdvisoryUpdatesRelationManager::class, [
        'ownerRecord' => $advisory,
        'pageClass' => ViewAdvisory::class,
    ])
        ->callAction(TestAction::make('viewOrEditAdvisoryUpdate')->table($advisoryUpdate), data: [
            'title' => 'An updated title',
            'update' => 'An updated update',
            'internal' => true,
            'status_id' => $newStatus->getKey(),
        ])
        ->assertHasNoActionErrors();

    expect($advisoryUpdate->refresh()->title)->toBe('An updated title')->and($advisoryUpdate->refresh()->update)->toBe('An updated update')->and($advisoryUpdate->refresh()->internal)->toBeTrue();

    expect($advisory->refresh()->status_id)->toBe($newStatus->getKey());
});

test('submitting the view or edit advisory update action that resolves the advisory hides the create action without a page refresh', function (): void {
    $advisory = Advisory::factory()->create([
        'status_id' => AdvisoryStatus::factory()->create([
            'classification' => SystemAdvisoryStatusClassification::Open,
        ])->getKey(),
    ]);

    $advisoryUpdate = AdvisoryUpdate::factory()->for($advisory, 'advisory')->create();

    $resolvedStatus = AdvisoryStatus::factory()->create([
        'classification' => SystemAdvisoryStatusClassification::Resolved,
    ]);

    asSuperAdmin();

    $component = Livewire::test(AdvisoryUpdatesRelationManager::class, [
        'ownerRecord' => $advisory,
        'pageClass' => ViewAdvisory::class,
    ])
        ->assertTableActionVisible('create')
        ->callAction(TestAction::make('viewOrEditAdvisoryUpdate')->table($advisoryUpdate), data: [
            'title' => $advisoryUpdate->title,
            'update' => $advisoryUpdate->update,
            'internal' => $advisoryUpdate->internal,
            'status_id' => $resolvedStatus->getKey(),
        ])
        ->assertHasNoActionErrors();

    $component->assertTableActionHidden('create');
});

// Permission Tests

test('AdvisoryUpdatesRelationManager create action is gated with proper access control', function (): void {
    $user = User::factory()->create();

    $advisory = Advisory::factory()->create([
        'status_id' => AdvisoryStatus::factory()->create([
            'classification' => SystemAdvisoryStatusClassification::Open,
        ])->getKey(),
    ]);

    $user->givePermissionTo('advisory.view-any', 'advisory.*.view', 'advisory_update.view-any');

    actingAs($user);

    Livewire::test(AdvisoryUpdatesRelationManager::class, [
        'ownerRecord' => $advisory,
        'pageClass' => ViewAdvisory::class,
    ])
        ->assertTableActionHidden('create');

    $user->givePermissionTo('advisory_update.create');

    Livewire::test(AdvisoryUpdatesRelationManager::class, [
        'ownerRecord' => $advisory,
        'pageClass' => ViewAdvisory::class,
    ])
        ->assertTableActionVisible('create');
});
