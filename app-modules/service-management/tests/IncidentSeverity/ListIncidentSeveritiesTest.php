<?php

use AidingApp\Authorization\Enums\LicenseType;
use AidingApp\ServiceManagement\Filament\Resources\IncidentSeverityResource;
use AidingApp\ServiceManagement\Filament\Resources\IncidentSeverityResource\Pages\ListIncidentSeverities;
use AidingApp\ServiceManagement\Models\Incident;
use AidingApp\ServiceManagement\Models\IncidentSeverity;
use App\Models\User;
use Filament\Tables\Actions\DeleteBulkAction;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertNotSoftDeleted;
use function Pest\Laravel\assertSoftDeleted;
use function Pest\Livewire\livewire;

test('ListIncidentSeverities is gated with proper access control', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user)
        ->get(
            IncidentSeverityResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('product_admin.view-any');

    actingAs($user)
        ->get(
            IncidentSeverityResource::getUrl('index')
        )->assertSuccessful();
});

test('can list records', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user)
        ->get(
            IncidentSeverityResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('product_admin.view-any');

    $records = IncidentSeverity::factory()->count(5)->create();

    livewire(ListIncidentSeverities::class)
        ->assertCountTableRecords(5)
        ->assertCanSeeTableRecords($records)
        ->assertSuccessful();
});

test('bulk delete IncidentSeverities', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user);

    $user->givePermissionTo('product_admin.view-any');
    $user->givePermissionTo('product_admin.*.delete');

    $incidentSeverities = IncidentSeverity::factory()->count(10)->create();

    livewire(ListIncidentSeverities::class)
        ->callTableBulkAction(DeleteBulkAction::class, $incidentSeverities);

    foreach ($incidentSeverities as $incidentSeverity) {
        assertSoftDeleted($incidentSeverity);
    }
});

test('prevent deletion of IncidentSeverity if it has associated Incidents', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user);

    $user->givePermissionTo('product_admin.view-any');
    $user->givePermissionTo('product_admin.*.delete');

    $incidentSeverities = IncidentSeverity::factory()
        ->has(Incident::factory(), 'incidents')
        ->count(2)
        ->create();

    livewire(ListIncidentSeverities::class)
        ->callTableBulkAction(DeleteBulkAction::class, $incidentSeverities);

    foreach ($incidentSeverities as $incidentSeverity) {
        assertNotSoftDeleted($incidentSeverity);
    }
});
