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
use AidingApp\ServiceManagement\Filament\Resources\IncidentUpdateResource;
use AidingApp\ServiceManagement\Filament\Resources\IncidentUpdateResource\Pages\CreateIncidentUpdate;
use AidingApp\ServiceManagement\Models\IncidentUpdate;
use AidingApp\ServiceManagement\Tests\RequestFactories\CreateIncidentUpdateRequestFactory;
use App\Models\User;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEmpty;
use function Tests\asSuperAdmin;

test('A successful action on the CreateIncidentUpdate page', function () {
    asSuperAdmin()
        ->get(
            IncidentResource::getUrl('create')
        )
        ->assertSuccessful();

    $request = collect(CreateIncidentUpdateRequestFactory::new()->create());

    livewire(CreateIncidentUpdate::class)
            ->fillForm($request->toArray())
            ->call('create')
            ->assertHasNoFormErrors();

    assertCount(1, IncidentUpdate::all());

    assertDatabaseHas(IncidentUpdate::class, $request->except('incident_id')->toArray());

    expect(IncidentUpdate::first()->incident->id)
        ->toEqual($request->get('incident_id'));
});

test('CreateIncidentUpdate requires valid data', function ($data, $errors) {
    asSuperAdmin();

    livewire(CreateIncidentUpdate::class)
        ->fillForm(CreateIncidentUpdateRequestFactory::new($data)->create())
        ->call('create')
        ->assertHasFormErrors($errors);

    assertEmpty(IncidentUpdate::all());
})->with(
    [
        'incident missing' => [CreateIncidentUpdateRequestFactory::new()->without('incident_id'), ['incident_id' => 'required']],
        'incident not existing incident id' => [CreateIncidentUpdateRequestFactory::new()->state(['incident_id' => fake()->uuid()]), ['incident_id' => 'exists']],
        'update missing' => [CreateIncidentUpdateRequestFactory::new()->without('update'), ['update' => 'required']],
        'update is not a string' => [CreateIncidentUpdateRequestFactory::new()->state(['update' => 99]), ['update' => 'string']],
        'internal not a boolean' => [CreateIncidentUpdateRequestFactory::new()->state(['internal' => 'invalid']), ['internal' => 'boolean']],
    ]
);

// Permission Tests

test('CreateIncidentUpdate is gated with proper access control', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user)
        ->get(
            IncidentUpdateResource::getUrl('create')
        )->assertForbidden();

    livewire(CreateIncidentUpdate::class)
        ->assertForbidden();

    $user->givePermissionTo('incident_update.view-any');
    $user->givePermissionTo('incident_update.create');

    actingAs($user)
        ->get(
            IncidentUpdateResource::getUrl('create')
        )->assertSuccessful();

    $request = collect(CreateIncidentUpdateRequestFactory::new()->create());

    livewire(CreateIncidentUpdate::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, IncidentUpdate::all());

    assertDatabaseHas(IncidentUpdate::class, $request->toArray());
});

test('CreateIncidentUpdate is gated with proper feature access control', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = false;

    $settings->save();

    $user = User::factory()->licensed(LicenseType::cases())->create();

    $user->givePermissionTo('incident_update.view-any');
    $user->givePermissionTo('incident_update.create');

    actingAs($user)
        ->get(
           IncidentUpdateResource::getUrl('create')
        )->assertForbidden();

    livewire(CreateIncidentUpdate::class)
        ->assertForbidden();

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    actingAs($user)
        ->get(
            IncidentUpdateResource::getUrl('create')
        )->assertSuccessful();

    $request = collect(CreateIncidentUpdateRequestFactory::new()->create());

    livewire(CreateIncidentUpdate::class)
        ->fillForm($request->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, IncidentUpdate::all());

    assertDatabaseHas(IncidentUpdate::class, $request->toArray());
});
