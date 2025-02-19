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
use AidingApp\ServiceManagement\Filament\Resources\IncidentSeverityResource;
use AidingApp\ServiceManagement\Filament\Resources\IncidentSeverityResource\Pages\EditIncidentSeverity;
use AidingApp\ServiceManagement\Models\IncidentSeverity;
use AidingApp\ServiceManagement\Tests\RequestFactories\IncidentSeverityRequestFactory;
use App\Models\User;
use Filament\Actions\DeleteAction;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

test('EditIncidentSeverity is gated with proper access control', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $incidentSeverity = IncidentSeverity::factory()->create();

    actingAs($user)
        ->get(
            IncidentSeverityResource::getUrl('edit', [
                'record' => $incidentSeverity,
            ])
        )->assertForbidden();

    livewire(EditIncidentSeverity::class, [
        'record' => $incidentSeverity->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('product_admin.view-any');
    $user->givePermissionTo('product_admin.*.update');

    actingAs($user)
        ->get(
            IncidentSeverityResource::getUrl('edit', [
                'record' => $incidentSeverity,
            ])
        )->assertSuccessful();

    $request = collect(IncidentSeverityRequestFactory::new()->create());

    livewire(EditIncidentSeverity::class, [
        'record' => $incidentSeverity->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    expect($incidentSeverity->fresh()->name)->toEqual($request->get('name'));
});

test('EditIncidentSeverity validates the inputs', function ($data, $errors) {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user);

    $user->givePermissionTo('product_admin.view-any');
    $user->givePermissionTo('product_admin.*.update');

    $incidentSeverity = IncidentSeverity::factory()->create();

    $request = IncidentSeverityRequestFactory::new($data)->create();

    livewire(EditIncidentSeverity::class, [
        'record' => $incidentSeverity->getRouteKey(),
    ])
        ->fillForm($request)
        ->call('save')
        ->assertHasFormErrors($errors);
})->with(
    [
        'name required' => [
            IncidentSeverityRequestFactory::new()->state(['name' => null]),
            ['name' => 'required'],
        ],
        'name string' => [
            IncidentSeverityRequestFactory::new()->state(['name' => 1]),
            ['name' => 'string'],
        ],
        'name max' => [
            IncidentSeverityRequestFactory::new()->state(['name' => str()->random(256)]),
            ['name' => 'max'],
        ],
        'color required' => [
            IncidentSeverityRequestFactory::new()->state(['color' => null]),
            ['color' => 'required'],
        ],
    ]
);

test('delete action visible with proper access control', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $incidentSeverity = IncidentSeverity::factory()->create();

    actingAs($user);

    $user->givePermissionTo('product_admin.view-any');
    $user->givePermissionTo('product_admin.*.update');

    livewire(EditIncidentSeverity::class, [
        'record' => $incidentSeverity->getRouteKey(),
    ])
        ->assertActionHidden(DeleteAction::class);

    $user->givePermissionTo('product_admin.*.delete');

    livewire(EditIncidentSeverity::class, [
        'record' => $incidentSeverity->getRouteKey(),
    ])
        ->assertActionVisible(DeleteAction::class);
});
