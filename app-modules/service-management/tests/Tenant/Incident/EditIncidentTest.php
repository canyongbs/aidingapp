<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

    Aiding App™ is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Aiding App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use AidingApp\Authorization\Enums\LicenseType;
use AidingApp\ServiceManagement\Filament\Resources\IncidentResource;
use AidingApp\ServiceManagement\Filament\Resources\IncidentResource\Pages\EditIncident;
use AidingApp\ServiceManagement\Models\Incident;
use AidingApp\ServiceManagement\Tests\Tenant\RequestFactories\IncidentRequestFactory;
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
