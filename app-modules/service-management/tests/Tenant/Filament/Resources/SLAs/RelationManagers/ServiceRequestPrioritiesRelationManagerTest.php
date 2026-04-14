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

use AidingApp\ServiceManagement\Filament\Actions\TableSelectAssociateAction;
use AidingApp\ServiceManagement\Filament\Resources\SLAs\Pages\EditSla;
use AidingApp\ServiceManagement\Filament\Resources\SLAs\RelationManagers\ServiceRequestPrioritiesRelationManager;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Models\Sla;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;

use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('can render the relation manager on the edit page', function () {
    asSuperAdmin();

    $sla = Sla::create([
        'name' => 'Test SLA',
        'response_seconds' => 3600,
        'resolution_seconds' => 7200,
    ]);

    livewire(ServiceRequestPrioritiesRelationManager::class, [
        'ownerRecord' => $sla,
        'pageClass' => EditSla::class,
    ])
        ->assertSuccessful();
});

it('lists priorities associated with the SLA', function () {
    asSuperAdmin();

    $sla = Sla::create([
        'name' => 'Test SLA',
        'response_seconds' => 3600,
        'resolution_seconds' => 7200,
    ]);

    $associatedPriority = ServiceRequestPriority::factory()
        ->create(['sla_id' => $sla->getKey()]);

    $unassociatedPriority = ServiceRequestPriority::factory()
        ->create(['sla_id' => null]);

    livewire(ServiceRequestPrioritiesRelationManager::class, [
        'ownerRecord' => $sla,
        'pageClass' => EditSla::class,
    ])
        ->assertCanSeeTableRecords([$associatedPriority])
        ->assertCanNotSeeTableRecords([$unassociatedPriority])
        ->assertCountTableRecords(1);
});

it('displays the correct columns for each priority', function () {
    asSuperAdmin();

    $sla = Sla::create([
        'name' => 'Test SLA',
        'response_seconds' => 3600,
        'resolution_seconds' => 7200,
    ]);

    $type = ServiceRequestType::factory()->create();

    $priority = ServiceRequestPriority::factory()->create([
        'sla_id' => $sla->getKey(),
        'type_id' => $type->getKey(),
        'name' => 'High Priority',
        'order' => 1,
    ]);

    $component = livewire(ServiceRequestPrioritiesRelationManager::class, [
        'ownerRecord' => $sla,
        'pageClass' => EditSla::class,
    ]);

    $component
        ->assertSuccessful()
        ->assertTableColumnExists('type.name')
        ->assertTableColumnExists('name')
        ->assertTableColumnExists('order')
        ->assertTableColumnExists('service_requests_count')
        ->assertTableColumnStateSet('name', 'High Priority', $priority)
        ->assertTableColumnStateSet('order', 1, $priority)
        ->assertTableColumnStateSet('type.name', $type->name, $priority);
});

it('has the associate action available', function () {
    asSuperAdmin();

    $sla = Sla::create([
        'name' => 'Test SLA',
        'response_seconds' => 3600,
        'resolution_seconds' => 7200,
    ]);

    livewire(ServiceRequestPrioritiesRelationManager::class, [
        'ownerRecord' => $sla,
        'pageClass' => EditSla::class,
    ])
        ->assertTableActionExists(TableSelectAssociateAction::class);
});

it('can associate a single priority to the SLA', function () {
    asSuperAdmin();

    $sla = Sla::create([
        'name' => 'Test SLA',
        'response_seconds' => 3600,
        'resolution_seconds' => 7200,
    ]);

    $priority = ServiceRequestPriority::factory()->create(['sla_id' => null]);

    livewire(ServiceRequestPrioritiesRelationManager::class, [
        'ownerRecord' => $sla,
        'pageClass' => EditSla::class,
    ])
        ->callTableAction(TableSelectAssociateAction::class, data: [
            'recordId' => [$priority->getKey()],
        ])
        ->assertHasNoTableActionErrors();

    $priority->refresh();

    expect($priority->sla_id)->toBe($sla->getKey());
});

it('can associate multiple priorities to the SLA', function () {
    asSuperAdmin();

    $sla = Sla::create([
        'name' => 'Test SLA',
        'response_seconds' => 3600,
        'resolution_seconds' => 7200,
    ]);

    $type = ServiceRequestType::factory()->create();

    $priorities = ServiceRequestPriority::factory()
        ->count(3)
        ->create([
            'sla_id' => null,
            'type_id' => $type->getKey(),
        ]);

    livewire(ServiceRequestPrioritiesRelationManager::class, [
        'ownerRecord' => $sla,
        'pageClass' => EditSla::class,
    ])
        ->callTableAction(TableSelectAssociateAction::class, data: [
            'recordId' => $priorities->pluck('id')->all(),
        ])
        ->assertHasNoTableActionErrors();

    $priorities->each(function (ServiceRequestPriority $priority) use ($sla) {
        $priority->refresh();
        expect($priority->sla_id)->toBe($sla->getKey());
    });
});

it('can associate priorities from different types to the SLA', function () {
    asSuperAdmin();

    $sla = Sla::create([
        'name' => 'Test SLA',
        'response_seconds' => 3600,
        'resolution_seconds' => 7200,
    ]);

    $typeA = ServiceRequestType::factory()->create();
    $typeB = ServiceRequestType::factory()->create();

    $priorityA = ServiceRequestPriority::factory()->create([
        'sla_id' => null,
        'type_id' => $typeA->getKey(),
    ]);

    $priorityB = ServiceRequestPriority::factory()->create([
        'sla_id' => null,
        'type_id' => $typeB->getKey(),
    ]);

    livewire(ServiceRequestPrioritiesRelationManager::class, [
        'ownerRecord' => $sla,
        'pageClass' => EditSla::class,
    ])
        ->callTableAction(TableSelectAssociateAction::class, data: [
            'recordId' => [$priorityA->getKey(), $priorityB->getKey()],
        ])
        ->assertHasNoTableActionErrors();

    $priorityA->refresh();
    $priorityB->refresh();

    expect($priorityA->sla_id)->toBe($sla->getKey())
        ->and($priorityB->sla_id)->toBe($sla->getKey());
});

it('can dissociate a priority from the SLA', function () {
    asSuperAdmin();

    $sla = Sla::create([
        'name' => 'Test SLA',
        'response_seconds' => 3600,
        'resolution_seconds' => 7200,
    ]);

    $priority = ServiceRequestPriority::factory()->create([
        'sla_id' => $sla->getKey(),
    ]);

    livewire(ServiceRequestPrioritiesRelationManager::class, [
        'ownerRecord' => $sla,
        'pageClass' => EditSla::class,
    ])
        ->callTableAction(DissociateAction::class, $priority)
        ->assertHasNoTableActionErrors();

    $priority->refresh();

    expect($priority->sla_id)->toBeNull();
});

it('can bulk dissociate priorities from the SLA', function () {
    asSuperAdmin();

    $sla = Sla::create([
        'name' => 'Test SLA',
        'response_seconds' => 3600,
        'resolution_seconds' => 7200,
    ]);

    $priorities = ServiceRequestPriority::factory()
        ->count(3)
        ->create(['sla_id' => $sla->getKey()]);

    livewire(ServiceRequestPrioritiesRelationManager::class, [
        'ownerRecord' => $sla,
        'pageClass' => EditSla::class,
    ])
        ->callTableBulkAction(DissociateBulkAction::class, $priorities)
        ->assertHasNoTableBulkActionErrors();

    $priorities->each(function (ServiceRequestPriority $priority) {
        $priority->refresh();
        expect($priority->sla_id)->toBeNull();
    });
});
