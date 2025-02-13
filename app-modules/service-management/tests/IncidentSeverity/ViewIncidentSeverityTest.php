<?php

use AidingApp\Authorization\Enums\LicenseType;
use AidingApp\ServiceManagement\Filament\Resources\IncidentSeverityResource;
use AidingApp\ServiceManagement\Models\IncidentSeverity;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Tests\asSuperAdmin;

test('The correct details are displayed on the ViewIncidentSeverity page', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user);

    $user->givePermissionTo('product_admin.view-any');
    $user->givePermissionTo('product_admin.*.view');

    $incidentSeverity = IncidentSeverity::factory()->create();

    asSuperAdmin()
        ->get(
            IncidentSeverityResource::getUrl('view', [
                'record' => $incidentSeverity,
            ])
        )
        ->assertSuccessful()
        ->assertSeeTextInOrder(
            [
                'Name',
                $incidentSeverity->name,
            ]
        );
});

test('ViewIncidentSeverity is gated with proper access control', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $incidentSeverity = IncidentSeverity::factory()->create();

    asSuperAdmin($user);

    actingAs($user)
        ->get(
            IncidentSeverityResource::getUrl('view', [
                'record' => $incidentSeverity,
            ])
        )->assertSuccessful();
});
