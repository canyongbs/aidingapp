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
        'domain url' => [
            ServiceMonitoringTargetRequestFactory::new()->state(['domain' => 'invalid-url']),
            ['domain' => 'url'],
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

test('create service monitoring with validate the domain url', function () {
    asSuperAdmin();

    $validUrls = [
        'http://example.com',
        'http://192.168.0.1',
        'http://192.168.0.1:8000',
    ];

    foreach ($validUrls as $url) {
        $request = ServiceMonitoringTarget::factory()->create(['domain' => $url])->toArray();

        livewire(CreateServiceMonitoring::class)
            ->fillForm($request)
            ->call('create')
            ->assertHasNoFormErrors();
    }

    $invalidUrls = [
        'ftp://example.com',
        'example..com',
        '://missing.scheme.com',
        '[]',
        'http://example',
    ];

    foreach ($invalidUrls as $url) {
        $request = ServiceMonitoringTarget::factory()->create(['domain' => $url])->toArray();

        livewire(CreateServiceMonitoring::class)
            ->fillForm($request)
            ->call('create')
            ->assertHasFormErrors(['domain']);
    }
});
