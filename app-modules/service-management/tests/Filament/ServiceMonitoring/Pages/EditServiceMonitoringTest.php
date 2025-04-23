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
use AidingApp\ServiceManagement\Filament\Resources\ServiceMonitoringResource;
use AidingApp\ServiceManagement\Filament\Resources\ServiceMonitoringResource\Pages\EditServiceMonitoring;
use AidingApp\ServiceManagement\Models\ServiceMonitoringTarget;
use AidingApp\ServiceManagement\Tests\RequestFactories\ServiceMonitoringTargetRequestFactory;
use App\Models\User;
use Filament\Actions\DeleteAction;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

test('EditServiceMonitoring is gated with proper access control', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $serviceMonitoringTarget = ServiceMonitoringTarget::factory()->create();

    actingAs($user)
        ->get(
            ServiceMonitoringResource::getUrl('edit', [
                'record' => $serviceMonitoringTarget,
            ])
        )->assertForbidden();

    livewire(EditServiceMonitoring::class, [
        'record' => $serviceMonitoringTarget->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('service_monitoring.view-any');
    $user->givePermissionTo('service_monitoring.*.update');

    actingAs($user)
        ->get(
            ServiceMonitoringResource::getUrl('edit', [
                'record' => $serviceMonitoringTarget,
            ])
        )->assertSuccessful();

    $request = collect(ServiceMonitoringTargetRequestFactory::new()->create());

    livewire(EditServiceMonitoring::class, [
        'record' => $serviceMonitoringTarget->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    $serviceMonitoringTarget->refresh();

    expect($serviceMonitoringTarget->fresh()->name)->toEqual($request->get('name'))
        ->and($serviceMonitoringTarget->description)->toEqual($request->get('description'))
        ->and($serviceMonitoringTarget->domain)->toEqual($request->get('domain'))
        ->and($serviceMonitoringTarget->frequency)->toEqual($request->get('frequency'));
});

test('EditServiceMonitoring validates the inputs', function ($data, $errors) {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user);

    $user->givePermissionTo('service_monitoring.view-any');
    $user->givePermissionTo('service_monitoring.*.update');

    $serviceMonitoringTarget = ServiceMonitoringTarget::factory()->create();

    $request = ServiceMonitoringTargetRequestFactory::new($data)->create();

    livewire(EditServiceMonitoring::class, [
        'record' => $serviceMonitoringTarget->getRouteKey(),
    ])
        ->fillForm($request)
        ->call('save')
        ->assertHasFormErrors($errors);
})->with(
    [
        'name required' => [
            ServiceMonitoringTargetRequestFactory::new()->state(['name' => null]),
            ['name' => 'required'],
        ],
        'name string' => [
            ServiceMonitoringTargetRequestFactory::new()->state(['name' => 1]),
            ['name' => 'string'],
        ],
        'name max' => [
            ServiceMonitoringTargetRequestFactory::new()->state(['name' => str()->random(256)]),
            ['name' => 'max'],
        ],
        'description max' => [
            ServiceMonitoringTargetRequestFactory::new()->state(['description' => str()->random(65536)]),
            ['description' => 'max'],
        ],
        'domain required' => [
            ServiceMonitoringTargetRequestFactory::new()->state(['domain' => null]),
            ['domain' => 'required'],
        ],
        'domain max' => [
            ServiceMonitoringTargetRequestFactory::new()->state(['domain' => str()->random(256)]),
            ['domain' => 'max'],
        ],
        'domain url' => [
            ServiceMonitoringTargetRequestFactory::new()->state(['domain' => 'invalid-url']),
            ['domain' => 'url'],
        ],
        'frequency required' => [
            ServiceMonitoringTargetRequestFactory::new()->state(['frequency' => null]),
            ['frequency' => 'required'],
        ],
    ]
);

test('delete action visible with proper access control', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $serviceMonitoringTarget = ServiceMonitoringTarget::factory()->create();

    actingAs($user);

    $user->givePermissionTo('service_monitoring.view-any');
    $user->givePermissionTo('service_monitoring.*.update');

    livewire(EditServiceMonitoring::class, [
        'record' => $serviceMonitoringTarget->getRouteKey(),
    ])
        ->assertActionHidden(DeleteAction::class);

    $user->givePermissionTo('service_monitoring.*.delete');

    livewire(EditServiceMonitoring::class, [
        'record' => $serviceMonitoringTarget->getRouteKey(),
    ])
        ->assertActionVisible(DeleteAction::class);
});
