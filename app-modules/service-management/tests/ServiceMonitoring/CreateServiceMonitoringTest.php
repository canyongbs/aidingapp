<?php

use AidingApp\Authorization\Enums\LicenseType;
use AidingApp\ServiceManagement\Filament\Resources\ServiceMonitoringResource;
use AidingApp\ServiceManagement\Filament\Resources\ServiceMonitoringResource\Pages\CreateServiceMonitoring;
use AidingApp\ServiceManagement\Models\ServiceMonitoringTarget;
use AidingApp\ServiceManagement\Tests\RequestFactories\ServiceMonitoringTargetRequestFactory;
use AidingApp\Team\Models\Team;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertCount;
use function Tests\asSuperAdmin;

test('CreateServiceMonitoring is gated with proper access control', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user)
        ->get(
            ServiceMonitoringResource::getUrl('create')
        )->assertForbidden();

    livewire(CreateServiceMonitoring::class)
        ->assertForbidden();

    $user->givePermissionTo('service_monitoring.view-any');
    $user->givePermissionTo('service_monitoring.create');

    actingAs($user)
        ->get(
            ServiceMonitoringResource::getUrl('create')
        )->assertSuccessful();

    $request = ServiceMonitoringTargetRequestFactory::new()->create();

    livewire(CreateServiceMonitoring::class)
        ->fillForm($request)
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, ServiceMonitoringTarget::all());

    assertDatabaseHas(ServiceMonitoringTarget::class, $request);
});

test('CreateServiceMonitoring validates the inputs', function ($data, $errors) {
    asSuperAdmin();

    $request = ServiceMonitoringTargetRequestFactory::new($data)->create();

    livewire(CreateServiceMonitoring::class)
        ->fillForm($request)
        ->call('create')
        ->assertHasFormErrors($errors);
})->with(
    [
        'name required' => [
            ServiceMonitoringTargetRequestFactory::new()->without('name'),
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
            ServiceMonitoringTargetRequestFactory::new()->without('domain'),
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
            ServiceMonitoringTargetRequestFactory::new()->without('frequency'),
            ['frequency' => 'required'],
        ],
    ]
);

test('CreateServiceMonitor with notification group User or Team', function () {
    asSuperAdmin();

    $serviceMonitoringTarget = ServiceMonitoringTarget::factory()->create();

    expect($serviceMonitoringTarget->teams())->exists()->toBeFalse();
    expect($serviceMonitoringTarget->users())->exists()->toBeFalse();

    $serviceMonitoringTarget->teams()->attach(Team::factory()->create());
    $serviceMonitoringTarget->users()->attach(User::factory()->create());

    expect($serviceMonitoringTarget->teams())->exists()->toBeTrue();
    expect($serviceMonitoringTarget->users())->exists()->toBeTrue();
});
