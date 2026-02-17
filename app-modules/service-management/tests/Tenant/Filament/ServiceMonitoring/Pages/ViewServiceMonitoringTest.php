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

use AidingApp\ServiceManagement\Filament\Resources\ServiceMonitoringResource;
use AidingApp\ServiceManagement\Filament\Resources\ServiceMonitoringResource\Pages\ViewServiceMonitoring;
use AidingApp\ServiceManagement\Models\ServiceMonitoringTarget;
use AidingApp\Team\Models\Team;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

test('The correct details are displayed on the ViewServiceMonitoring page', function () {
    $user = User::factory()->create();

    actingAs($user);

    $user->givePermissionTo('service_monitoring.view-any');
    $user->givePermissionTo('service_monitoring.*.view');

    $serviceMonitoringTarget = ServiceMonitoringTarget::factory()
        ->hasAttached(Team::factory())
        ->hasAttached(User::factory())
        ->create();

    asSuperAdmin()
        ->get(
            ServiceMonitoringResource::getUrl('view', [
                'record' => $serviceMonitoringTarget,
            ])
        )
        ->assertSuccessful()
        ->assertSeeInOrder(
            [
                'Name',
                $serviceMonitoringTarget->name,
                'Description',
                $serviceMonitoringTarget->description,
                'URL',
                $serviceMonitoringTarget->domain,
                'Frequency',
                $serviceMonitoringTarget->frequency->getLabel(),
                'Teams',
                ...$serviceMonitoringTarget->teams()->pluck('name')->all(),
                'Users',
                ...$serviceMonitoringTarget->users()->pluck('name')->all(),
            ]
        );
});

test('ViewServiceMonitoring is gated with proper access control', function () {
    $user = User::factory()->create();

    $serviceMonitoringTarget = ServiceMonitoringTarget::factory()->create();

    asSuperAdmin($user);

    actingAs($user)
        ->get(
            ServiceMonitoringResource::getUrl('view', [
                'record' => $serviceMonitoringTarget,
            ])
        )->assertSuccessful();
});

test('Reset Monitoring button resets monitoring', function () {
    $user = User::factory()->create();

    asSuperAdmin($user);

    $serviceMonitoringTarget = ServiceMonitoringTarget::factory()->create();

    $serviceMonitoringTarget->histories()->create([
        'response' => 200,
        'response_time' => 0.138348,
        'succeeded' => 1,
    ]);

    expect($serviceMonitoringTarget->histories()->count())
        ->toBe(1);

    livewire(ViewServiceMonitoring::class, [
        'record' => $serviceMonitoringTarget->getRouteKey(),
    ])
        ->assertSuccessful()
        ->assertSee('Reset Monitoring')
        ->callAction('reset');

    expect($serviceMonitoringTarget->histories()->count())
        ->toBe(0);
});
