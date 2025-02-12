<?php

use AidingApp\Contact\Models\Contact;
use AidingApp\ServiceManagement\Filament\Resources\IncidentResource;
use AidingApp\ServiceManagement\Filament\Resources\IncidentResource\Pages\ListIncidents;
use AidingApp\ServiceManagement\Models\Incident;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

test('ListIncidents is gated with proper access control', function () {
    $user = User::factory()->licensed(Contact::getLicenseType())->create();

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

// test('can list records', function () {
//     $user = User::factory()->licensed(Contact::getLicenseType())->create();

//     actingAs($user)
//         ->get(
//             IncidentResource::getUrl('index')
//         )->assertForbidden();

//     $user->givePermissionTo('incident.view-any');

//     $records = Incident::factory()->count(5)->create();

//     livewire(ListIncidents::class)
//         ->assertCountTableRecords(6)
//         ->sortTable('id', 'desc')
//         ->assertCanSeeTableRecords($records)
//         ->assertSuccessful();
// });
