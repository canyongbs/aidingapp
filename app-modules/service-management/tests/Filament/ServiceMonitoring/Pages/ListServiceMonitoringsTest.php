<?php

use AidingApp\Authorization\Enums\LicenseType;
use AidingApp\ServiceManagement\Filament\Resources\ServiceMonitoringResource;
use AidingApp\ServiceManagement\Filament\Resources\ServiceMonitoringResource\Pages\ListServiceMonitorings;
use AidingApp\ServiceManagement\Models\ServiceMonitoringTarget;
use App\Models\User;
use Filament\Tables\Actions\DeleteBulkAction;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertSoftDeleted;
use function Pest\Livewire\livewire;

test('ListServiceMonitorings is gated with proper access control', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user)
        ->get(
            ServiceMonitoringResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('service_monitoring.view-any');

    actingAs($user)
        ->get(
            ServiceMonitoringResource::getUrl('index')
        )->assertSuccessful();
});

test('can list records', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user)
        ->get(
            ServiceMonitoringResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('service_monitoring.view-any');

    $records = ServiceMonitoringTarget::factory()->count(5)->create();

    livewire(ListServiceMonitorings::class)
        ->assertCountTableRecords(5)
        ->assertCanSeeTableRecords($records)
        ->assertSuccessful();
});

test('bulk delete ServiceMonitorings', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user);

    $user->givePermissionTo('service_monitoring.view-any');
    $user->givePermissionTo('service_monitoring.*.delete');

    $serviceMonitoringTargets = ServiceMonitoringTarget::factory()->count(10)->create();

    livewire(ListServiceMonitorings::class)
        ->callTableBulkAction(DeleteBulkAction::class, $serviceMonitoringTargets);

    foreach ($serviceMonitoringTargets as $serviceMonitoringTarget) {
        assertSoftDeleted($serviceMonitoringTarget);
    }
});
