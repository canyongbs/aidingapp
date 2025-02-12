<?php

use AidingApp\Authorization\Enums\LicenseType;
use AidingApp\ServiceManagement\Filament\Resources\IncidentResource;
use AidingApp\ServiceManagement\Filament\Resources\IncidentResource\Pages\CreateIncident;
use AidingApp\ServiceManagement\Models\Incident;
use AidingApp\ServiceManagement\Tests\RequestFactories\IncidentRequestFactory;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertCount;
use function Tests\asSuperAdmin;

test('CreateIncident is gated with proper access control', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user)
        ->get(
            IncidentResource::getUrl('create')
        )->assertForbidden();

    livewire(CreateIncident::class)
        ->assertForbidden();

    $user->givePermissionTo('incident.view-any');
    $user->givePermissionTo('incident.create');

    actingAs($user)
        ->get(
            IncidentResource::getUrl('create')
        )->assertSuccessful();

    $request = IncidentRequestFactory::new()->create();

    livewire(CreateIncident::class)
        ->fillForm($request)
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, Incident::all());

    assertDatabaseHas(Incident::class, $request);
});

test('CreateIncident validates the inputs', function ($data, $errors) {
    asSuperAdmin();

    $request = IncidentRequestFactory::new($data)->create();

    livewire(CreateIncident::class)
        ->fillForm($request)
        ->call('create')
        ->assertHasFormErrors($errors);
})->with(
    [
        'title required' => [
            IncidentRequestFactory::new()->without('title'),
            ['title' => 'required'],
        ],
        'title string' => [
            IncidentRequestFactory::new()->state(['title' => 1]),
            ['title' => 'string'],
        ],
        'title max' => [
            IncidentRequestFactory::new()->state(['title' => str()->random(256)]),
            ['title' => 'max'],
        ],
        'description required' => [
            IncidentRequestFactory::new()->without('description'),
            ['description' => 'required'],
        ],
        'description max' => [
            IncidentRequestFactory::new()->state(['description' => str()->random(65536)]),
            ['description' => 'max'],
        ],
        'severity_id missing' => [
            IncidentRequestFactory::new()->without('severity_id'),
            ['severity_id' => 'required'],
        ],
        'severity_id does not exist' => [
            IncidentRequestFactory::new()->state(['severity_id' => fake()->uuid()]),
            ['severity_id' => 'exists'],
        ],
        'status_id missing' => [
            IncidentRequestFactory::new()->without('status_id'),
            ['status_id' => 'required'],
        ],
        'status_id does not exist' => [
            IncidentRequestFactory::new()->state(['status_id' => fake()->uuid()]),
            ['status_id' => 'exists'],
        ],
        'assigned_team_id does not exist' => [
            IncidentRequestFactory::new()->state(['assigned_team_id' => fake()->uuid()]),
            ['assigned_team_id' => 'exists'],
        ],
    ]
);
