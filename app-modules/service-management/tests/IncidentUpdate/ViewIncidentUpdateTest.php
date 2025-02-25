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
use AidingApp\ServiceManagement\Filament\Resources\IncidentUpdateResource;
use AidingApp\ServiceManagement\Models\IncidentUpdate;
use App\Models\User;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Tests\asSuperAdmin;

test('The correct details are displayed on the ViewIncidentUpdate page', function () {
    $incidentUpdate = IncidentUpdate::factory()->create();

    asSuperAdmin()
        ->get(
            IncidentUpdateResource::getUrl('view', [
                'record' => $incidentUpdate,
            ])
        )
        ->assertSuccessful()
        ->assertSeeTextInOrder(
            [
                'Incident',
                $incidentUpdate->incident->title,
                'Internal',
                // TODO: Figure out how to check whether this internal value the check or the X icon0
                'Update',
                $incidentUpdate->update,
            ]
        );
});

// Permission Tests

test('ViewIncidentUpdate is gated with proper access control', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $incidentUpdate = IncidentUpdate::factory()->create();

    actingAs($user)
        ->get(
            IncidentUpdateResource::getUrl('view', [
                'record' => $incidentUpdate,
            ])
        )->assertForbidden();

    $user->givePermissionTo('incident_update.view-any');
    $user->givePermissionTo('incident_update.*.view');

    actingAs($user)
        ->get(
            IncidentUpdateResource::getUrl('view', [
                'record' => $incidentUpdate,
            ])
        )->assertSuccessful();
});

test('ViewIncidentUpdate is gated with proper feature access control', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = false;

    $settings->save();

    $user = User::factory()->licensed(LicenseType::cases())->create();

    $user->givePermissionTo('incident_update.view-any');
    $user->givePermissionTo('incident_update.*.view');

    $incidentUpdate = IncidentUpdate::factory()->create();

    actingAs($user)
        ->get(
            IncidentUpdateResource::getUrl('view', [
                'record' => $incidentUpdate,
            ])
        )->assertForbidden();

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    actingAs($user)
        ->get(
            IncidentUpdateResource::getUrl('view', [
                'record' => $incidentUpdate,
            ])
        )->assertSuccessful();
});
