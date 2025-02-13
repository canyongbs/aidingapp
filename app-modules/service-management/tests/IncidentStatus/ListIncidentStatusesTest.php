<?php

use AidingApp\Authorization\Enums\LicenseType;
use AidingApp\ServiceManagement\Filament\Resources\IncidentStatusResource;
use AidingApp\ServiceManagement\Filament\Resources\IncidentStatusResource\Pages\ListIncidentStatuses;
use AidingApp\ServiceManagement\Models\Incident;
use AidingApp\ServiceManagement\Models\IncidentStatus;
use App\Models\User;
use Filament\Tables\Actions\DeleteBulkAction;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertNotSoftDeleted;
use function Pest\Laravel\assertSoftDeleted;
use function Pest\Livewire\livewire;

test('ListIncidentStatuses is gated with proper access control', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user)
        ->get(
            IncidentStatusResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('product_admin.view-any');

    actingAs($user)
        ->get(
            IncidentStatusResource::getUrl('index')
        )->assertSuccessful();
});

test('can list records', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user)
        ->get(
            IncidentStatusResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('product_admin.view-any');

    $records = IncidentStatus::factory()->count(5)->create();

    livewire(ListIncidentStatuses::class)
        ->assertCountTableRecords(5)
        ->assertCanSeeTableRecords($records)
        ->assertSuccessful();
});

test('bulk delete IncidentStatuses', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user);

    $user->givePermissionTo('product_admin.view-any');
    $user->givePermissionTo('product_admin.*.delete');

    $incidentStatuses = IncidentStatus::factory()->count(10)->create();

    livewire(ListIncidentStatuses::class)
        ->callTableBulkAction(DeleteBulkAction::class, $incidentStatuses);

    foreach ($incidentStatuses as $incidentStatus) {
        assertSoftDeleted($incidentStatus);
    }
});

test('prevent deletion of IncidentStatus if it has associated Incidents', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user);

    $user->givePermissionTo('product_admin.view-any');
    $user->givePermissionTo('product_admin.*.delete');

    $incidentStatuses = IncidentStatus::factory()
        ->has(Incident::factory(), 'incidents')
        ->count(2)
        ->create();

    livewire(ListIncidentStatuses::class)
        ->callTableBulkAction(DeleteBulkAction::class, $incidentStatuses);

    foreach ($incidentStatuses as $incidentStatus) {
        assertNotSoftDeleted($incidentStatus);
    }
});
