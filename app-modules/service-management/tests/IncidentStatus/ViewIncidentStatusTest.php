<?php

use AidingApp\Authorization\Enums\LicenseType;
use AidingApp\ServiceManagement\Filament\Resources\IncidentStatusResource;
use AidingApp\ServiceManagement\Models\IncidentStatus;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Tests\asSuperAdmin;

test('The correct details are displayed on the ViewIncidentStatus page', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user);

    $user->givePermissionTo('product_admin.view-any');
    $user->givePermissionTo('product_admin.*.view');

    $incidentStatus = IncidentStatus::factory()->create();

    asSuperAdmin()
        ->get(
            IncidentStatusResource::getUrl('view', [
                'record' => $incidentStatus,
            ])
        )
        ->assertSuccessful()
        ->assertSeeTextInOrder(
            [
                'Name',
                $incidentStatus->name,
                'Classification',
                $incidentStatus->classification->name,
            ]
        );
});

test('ViewIncidentStatus is gated with proper access control', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $incidentStatus = IncidentStatus::factory()->create();

    asSuperAdmin($user);

    actingAs($user)
        ->get(
            IncidentStatusResource::getUrl('view', [
                'record' => $incidentStatus,
            ])
        )->assertSuccessful();
});
