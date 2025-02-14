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
use AidingApp\ServiceManagement\Filament\Resources\IncidentStatusResource;
use AidingApp\ServiceManagement\Filament\Resources\IncidentStatusResource\Pages\EditIncidentStatus;
use AidingApp\ServiceManagement\Models\IncidentStatus;
use AidingApp\ServiceManagement\Tests\RequestFactories\IncidentStatusRequestFactory;
use App\Models\User;
use Filament\Actions\DeleteAction;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

test('EditIncidentStatus is gated with proper access control', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $incidentStatus = IncidentStatus::factory()->create();

    actingAs($user)
        ->get(
            IncidentStatusResource::getUrl('edit', [
                'record' => $incidentStatus,
            ])
        )->assertForbidden();

    livewire(EditIncidentStatus::class, [
        'record' => $incidentStatus->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('product_admin.view-any');
    $user->givePermissionTo('product_admin.*.update');

    actingAs($user)
        ->get(
            IncidentStatusResource::getUrl('edit', [
                'record' => $incidentStatus,
            ])
        )->assertSuccessful();

    $request = collect(IncidentStatusRequestFactory::new()->create());

    livewire(EditIncidentStatus::class, [
        'record' => $incidentStatus->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    expect($incidentStatus->fresh()->name)->toEqual($request->get('name'))
        ->and($incidentStatus->fresh()->classification)->toEqual($request->get('classification'));
});

test('EditIncidentStatus validates the inputs', function ($data, $errors) {
    asSuperAdmin();

    $incidentStatus = IncidentStatus::factory()->create();

    $request = collect(IncidentStatusRequestFactory::new($data)->create());

    livewire(EditIncidentStatus::class, [
        'record' => $incidentStatus->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasFormErrors($errors);
})->with(
    [
        'name required' => [
            IncidentStatusRequestFactory::new()->state(['name' => null]),
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
        'classification required' => [
            IncidentStatusRequestFactory::new()->state(['classification' => null]),
            ['classification' => 'required'],
        ],
    ]
);

test('delete action visible with proper access control', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $incidentStatus = IncidentStatus::factory()->create();

    actingAs($user);

    $user->givePermissionTo('product_admin.view-any');
    $user->givePermissionTo('product_admin.*.update');

    livewire(EditIncidentStatus::class, [
        'record' => $incidentStatus->getRouteKey(),
    ])
        ->assertActionHidden(DeleteAction::class);

    $user->givePermissionTo('product_admin.*.delete');

    livewire(EditIncidentStatus::class, [
        'record' => $incidentStatus->getRouteKey(),
    ])
        ->assertActionVisible(DeleteAction::class);
});
