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

use AidingApp\Contact\Models\Contact;
use AidingApp\ServiceManagement\Filament\Resources\IncidentUpdateResource;
use AidingApp\ServiceManagement\Filament\Resources\IncidentUpdateResource\Pages\ListIncidentUpdates;
use AidingApp\ServiceManagement\Models\Incident;
use AidingApp\ServiceManagement\Models\IncidentUpdate;
use App\Models\User;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

test('The records are displayed on the ListIncidentUpdates page', function () {
    $incidentUpdates = IncidentUpdate::factory()
        ->for(Incident::factory(), 'incident')
        ->count(10)
        ->create();

    asSuperAdmin();

    $component = livewire(ListIncidentUpdates::class);

    $component->assertSuccessful()
        ->assertCanSeeTableRecords($incidentUpdates)
        ->assertCountTableRecords(10);
});

// Permission Tests

test('ListIncidentUpdates is gated with proper access control', function () {
    $user = User::factory()->licensed([Contact::getLicenseType()])->create();

    actingAs($user)
        ->get(
            IncidentUpdateResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('incident_update.view-any');

    actingAs($user)
        ->get(
            IncidentUpdateResource::getUrl('index')
        )->assertSuccessful();
});

test('ListIncidentUpdates is gated with proper feature access control', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = false;

    $settings->save();

    $user = User::factory()->licensed([Contact::getLicenseType()])->create();

    $user->givePermissionTo('incident_update.view-any');

    actingAs($user)
        ->get(
            IncidentUpdateResource::getUrl()
        )->assertForbidden();

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    actingAs($user)
        ->get(
            IncidentUpdateResource::getUrl()
        )->assertSuccessful();
});
