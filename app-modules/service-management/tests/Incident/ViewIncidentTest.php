<?php

use AidingApp\Authorization\Enums\LicenseType;
use AidingApp\ServiceManagement\Filament\Resources\IncidentResource;
use AidingApp\ServiceManagement\Models\Incident;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Tests\asSuperAdmin;

test('The correct details are displayed on the ViewIncident page', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user);

    $user->givePermissionTo('incident.view-any');
    $user->givePermissionTo('incident.*.view');

    $incident = Incident::factory()->create();

    asSuperAdmin()
        ->get(
            IncidentResource::getUrl('view', [
                'record' => $incident,
            ])
        )
        ->assertSuccessful()
        ->assertSeeTextInOrder(
            [
                'Title',
                $incident->title,
                'Description',
                $incident->description,
                'Severity',
                $incident->severity->name,
                'Status',
                $incident->status->name,
                'Assigned Team',
                $incident->assignedTeam->name,
            ]
        );
});

test('ViewIncident is gated with proper access control', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $incident = Incident::factory()->create();

    asSuperAdmin($user);

    actingAs($user)
        ->get(
            IncidentResource::getUrl('view', [
                'record' => $incident,
            ])
        )->assertSuccessful();
});
