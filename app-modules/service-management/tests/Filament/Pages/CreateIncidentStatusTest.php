<?php

use AidingApp\Authorization\Enums\LicenseType;
use AidingApp\ServiceManagement\Filament\Resources\IncidentResource;
use AidingApp\ServiceManagement\Filament\Resources\IncidentResource\Pages\CreateIncident;
use AidingApp\ServiceManagement\Filament\Resources\IncidentStatusResource;
use AidingApp\ServiceManagement\Filament\Resources\IncidentStatusResource\Pages\CreateIncidentStatus;
use AidingApp\ServiceManagement\Models\Incident;
use AidingApp\ServiceManagement\Models\IncidentStatus;
use AidingApp\ServiceManagement\Tests\RequestFactories\IncidentRequestFactory;
use AidingApp\ServiceManagement\Tests\RequestFactories\IncidentStatusRequestFactory;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertCount;
use function Tests\asSuperAdmin;

// test('A successful action on the CreateIncidentStatus page', function () {
//     asSuperAdmin()
//         ->get(
//             IncidentStatusResource::getUrl('create')
//         )
//         ->assertSuccessful();

//     $request = IncidentStatusRequestFactory::new()->create();

//     livewire(CreateIncidentStatus::class)
//         ->fillForm($request)
//         ->call('create')
//         ->assertHasNoFormErrors();

//     assertCount(1, IncidentStatus::all());

//     assertDatabaseHas(IncidentStatus::class, $request);
// });

test('CreateIncidentStatus requires valid data', function ($data, $errors) {
    asSuperAdmin();

    $incidentStatus = collect(IncidentStatusRequestFactory::new($data)->create());

    livewire(CreateIncidentStatus::class)
        ->fillForm($incidentStatus->toArray())
        ->call('create')
        ->assertHasFormErrors($errors);
})->with(
    [
        'name required' => [
            IncidentStatusRequestFactory::new()->without('name'),
            ['name' => 'required'],
        ],
        'name string' => [
            IncidentStatusRequestFactory::new()->state(['name' => 1]),
            ['name' => 'string'],
        ],
        'name max' => [
            IncidentStatusRequestFactory::new()->state(['name' => str()->random(256)]),
            ['name' => 'max'],
        ],
        //classification
    ]
);

// test('CreateIncidentStatus is gated with proper access control', function () {
//     $user = User::factory()->licensed(LicenseType::cases())->create();

//     actingAs($user)
//         ->get(
//             IncidentStatusResource::getUrl('create')
//         )->assertForbidden();

//     livewire(CreateIncidentStatus::class)
//         ->assertForbidden();

//     $user->givePermissionTo('incident.view-any');
//     $user->givePermissionTo('incident.create');

//     actingAs($user)
//         ->get(
//             IncidentResource::getUrl('create')
//         )->assertSuccessful();

//     // $request = collect(IncidentRequestFactory::new()->create([
//     //     'created_by_id' => $user->id,
//     // ]));

//     $request = collect(IncidentRequestFactory::new()->create());

//     livewire(CreateIncident::class)
//         ->fillForm($request->toArray())
//         ->call('create')
//         ->assertHasNoFormErrors();

//     assertCount(1, Incident::all());

//     assertDatabaseHas(Incident::class, $request->toArray());
// });
