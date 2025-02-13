<?php

use AidingApp\Authorization\Enums\LicenseType;
use AidingApp\ServiceManagement\Filament\Resources\IncidentResource;
use AidingApp\ServiceManagement\Filament\Resources\IncidentResource\Pages\EditIncident;
use AidingApp\ServiceManagement\Models\Incident;
use AidingApp\ServiceManagement\Tests\RequestFactories\IncidentRequestFactory;
use App\Models\User;
use Filament\Actions\DeleteAction;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

test('EditIncident is gated with proper access control', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $incident = Incident::factory()->create();

    actingAs($user)
        ->get(
            IncidentResource::getUrl('edit', [
                'record' => $incident,
            ])
        )->assertForbidden();

    livewire(EditIncident::class, [
        'record' => $incident->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('incident.view-any');
    $user->givePermissionTo('incident.*.update');

    actingAs($user)
        ->get(
            IncidentResource::getUrl('edit', [
                'record' => $incident,
            ])
        )->assertSuccessful();

    $request = collect(IncidentRequestFactory::new()->create());

    livewire(EditIncident::class, [
        'record' => $incident->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    expect($incident->fresh()->title)->toEqual($request->get('title'))
        ->and($incident->fresh()->description)->toEqual($request->get('description'))
        ->and($incident->fresh()->severity_id)->toEqual($request->get('severity_id'))
        ->and($incident->fresh()->status_id)->toEqual($request->get('status_id'))
        ->and($incident->fresh()->assigned_team_id)->toEqual($request->get('assigned_team_id'));
});

test('EditIncident validates the inputs', function ($data, $errors) {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user);

    $user->givePermissionTo('incident.view-any');
    $user->givePermissionTo('incident.*.update');

    $incident = Incident::factory()->create();

    $request = IncidentRequestFactory::new($data)->create();

    livewire(EditIncident::class, [
        'record' => $incident->getRouteKey(),
    ])
        ->fillForm($request)
        ->call('save')
        ->assertHasFormErrors($errors);
})->with(
    [
        'title required' => [
            IncidentRequestFactory::new()->state(['title' => null]),
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
            IncidentRequestFactory::new()->state(['description' => null]),
            ['description' => 'required'],
        ],
        'description max' => [
            IncidentRequestFactory::new()->state(['description' => str()->random(65536)]),
            ['description' => 'max'],
        ],
        'severity_id missing' => [
            IncidentRequestFactory::new()->state(['severity_id' => null]),
            ['severity_id' => 'required'],
        ],
        'severity_id does not exist' => [
            IncidentRequestFactory::new()->state(['severity_id' => fake()->uuid()]),
            ['severity_id' => 'exists'],
        ],
        'status_id missing' => [
            IncidentRequestFactory::new()->state(['status_id' => null]),
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

test('delete action visible with proper access control', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $incident = Incident::factory()->create();

    actingAs($user);

    $user->givePermissionTo('incident.view-any');
    $user->givePermissionTo('incident.*.update');

    livewire(EditIncident::class, [
        'record' => $incident->getRouteKey(),
    ])
        ->assertActionHidden(DeleteAction::class);

    $user->givePermissionTo('incident.*.delete');

    livewire(EditIncident::class, [
        'record' => $incident->getRouteKey(),
    ])
        ->assertActionVisible(DeleteAction::class);
});
