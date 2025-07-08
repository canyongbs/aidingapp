<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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
use AidingApp\ServiceManagement\Filament\Resources\IncidentResource\Pages\CreateIncident;
use AidingApp\ServiceManagement\Models\Incident;
use AidingApp\ServiceManagement\Tests\Tenant\RequestFactories\IncidentRequestFactory;
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
