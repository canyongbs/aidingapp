<?php

use AidingApp\Authorization\Enums\LicenseType;
use AidingApp\ServiceManagement\Filament\Resources\IncidentResource;
use AidingApp\ServiceManagement\Filament\Resources\IncidentResource\Pages\ListIncidents;
use AidingApp\ServiceManagement\Models\Incident;
use App\Models\User;
use Filament\Tables\Actions\DeleteBulkAction;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertSoftDeleted;
use function Pest\Livewire\livewire;

test('ListIncidents is gated with proper access control', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user)
        ->get(
            IncidentResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('incident.view-any');

    actingAs($user)
        ->get(
            IncidentResource::getUrl('index')
        )->assertSuccessful();
});

test('can list records', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user)
        ->get(
            IncidentResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('incident.view-any');

    $records = Incident::factory()->count(5)->create();

    livewire(ListIncidents::class)
        ->assertCountTableRecords(5)
        ->assertCanSeeTableRecords($records)
        ->assertSuccessful();
});

test('bulk delete Incidents', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user);

    $user->givePermissionTo('incident.view-any');
    $user->givePermissionTo('incident.*.delete');

    $incidents = Incident::factory()->count(10)->create();

    livewire(ListIncidents::class)
        ->callTableBulkAction(DeleteBulkAction::class, $incidents);

    foreach ($incidents as $incident) {
        assertSoftDeleted($incident);
    }
});
