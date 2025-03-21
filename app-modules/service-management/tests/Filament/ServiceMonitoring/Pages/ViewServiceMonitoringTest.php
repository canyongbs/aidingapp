<?php

use AidingApp\Authorization\Enums\LicenseType;
use AidingApp\ServiceManagement\Filament\Resources\ServiceMonitoringResource;
use AidingApp\ServiceManagement\Models\ServiceMonitoringTarget;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Tests\asSuperAdmin;

test('The correct details are displayed on the ViewServiceMonitoring page', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user);

    $user->givePermissionTo('service_monitoring.view-any');
    $user->givePermissionTo('service_monitoring.*.view');

    $serviceMonitoringTarget = ServiceMonitoringTarget::factory()->create();

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
                $serviceMonitoringTarget->frequency,
                'Team',
                $serviceMonitoringTarget->teams()->pluck('name')->join(', '),
                'User',
                $serviceMonitoringTarget->users()->pluck('name')->join(', '),
            ]
        );
});

test('ViewServiceMonitoring is gated with proper access control', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $serviceMonitoringTarget = ServiceMonitoringTarget::factory()->create();

    asSuperAdmin($user);

    actingAs($user)
        ->get(
            ServiceMonitoringResource::getUrl('view', [
                'record' => $serviceMonitoringTarget,
            ])
        )->assertSuccessful();
});
