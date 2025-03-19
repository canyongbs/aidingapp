<?php

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

    expect($serviceMonitoringTarget->fresh()->name)->toEqual($request->get('name'))
        ->and($serviceMonitoringTarget->fresh()->description)->toEqual($request->get('description'))
        ->and($serviceMonitoringTarget->fresh()->domain)->toEqual($request->get('domain'))
        ->and($serviceMonitoringTarget->fresh()->frequency)->toEqual($request->get('frequency')->value);
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
        'domain regex' => [
            ServiceMonitoringTargetRequestFactory::new()->state(['domain' => 'invalid']),
            ['domain' => 'regex'],
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
