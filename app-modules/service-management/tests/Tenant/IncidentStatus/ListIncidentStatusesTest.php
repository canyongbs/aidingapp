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
use AidingApp\Authorization\Enums\LicenseType;
use AidingApp\ServiceManagement\Filament\Resources\IncidentStatusResource;
use AidingApp\ServiceManagement\Filament\Resources\IncidentStatusResource\Pages\ListIncidentStatuses;
use AidingApp\ServiceManagement\Models\Incident;
use AidingApp\ServiceManagement\Models\IncidentStatus;
use App\Models\User;
use App\Settings\LicenseSettings;
use Filament\Actions\DeleteBulkAction;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertNotSoftDeleted;
use function Pest\Laravel\assertSoftDeleted;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;

test('ListIncidentStatuses is gated with proper access control', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user)
        ->get(
            IncidentStatusResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('settings.view-any');

    actingAs($user)
        ->get(
            IncidentStatusResource::getUrl('index')
        )->assertSuccessful();
});

test('it is gated with proper feature access control', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->incidentManagement = false;
    $settings->save();

    $user = User::factory()->licensed(LicenseType::cases())->create();

    $user->givePermissionTo('settings.view-any');

    actingAs($user);

    get(ListIncidentStatuses::getUrl())->assertForbidden();

    $settings->data->addons->incidentManagement = true;
    $settings->save();

    $user->revokePermissionTo('settings.view-any');

    get(ListIncidentStatuses::getUrl())->assertForbidden();

    $user->givePermissionTo('settings.view-any');

    get(ListIncidentStatuses::getUrl())->assertSuccessful();
});

test('can list records', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user)
        ->get(
            IncidentStatusResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('settings.view-any');

    $records = IncidentStatus::factory()->count(5)->create();

    livewire(ListIncidentStatuses::class)
        ->assertCountTableRecords(5)
        ->assertCanSeeTableRecords($records)
        ->assertSuccessful();
});

test('bulk delete IncidentStatuses', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user);

    $user->givePermissionTo('settings.view-any');
    $user->givePermissionTo('settings.*.delete');

    $incidentStatuses = IncidentStatus::factory()->count(10)->create();

    livewire(ListIncidentStatuses::class)
        ->callTableBulkAction(DeleteBulkAction::class, $incidentStatuses);

    foreach ($incidentStatuses as $incidentStatus) {
        assertSoftDeleted($incidentStatus);
    }
});

test('prevent deletion of IncidentStatus if it has associated Incidents', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    actingAs($user);

    $user->givePermissionTo('settings.view-any');
    $user->givePermissionTo('settings.*.delete');

    $incidentStatuses = IncidentStatus::factory()
        ->has(Incident::factory(), 'incidents')
        ->count(2)
        ->create();

    livewire(ListIncidentStatuses::class)
        ->callTableBulkAction(DeleteBulkAction::class, $incidentStatuses);

    foreach ($incidentStatuses as $incidentStatus) {
        assertNotSoftDeleted($incidentStatus);
    }
});
