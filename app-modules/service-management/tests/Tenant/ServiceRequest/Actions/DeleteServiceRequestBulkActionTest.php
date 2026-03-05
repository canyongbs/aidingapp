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

use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestResource\Pages\ListServiceRequests;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\Team\Models\Team;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

test('it can bulk delete service requests as super admin', function () {
    asSuperAdmin();

    $serviceRequests = ServiceRequest::factory()
        ->count(3)
        ->create();

    livewire(ListServiceRequests::class)
        ->assertSuccessful()
        ->callTableBulkAction('delete', $serviceRequests);

    foreach ($serviceRequests as $sr) {
        expect(ServiceRequest::find($sr->getKey()))->toBeNull();
    }
});

test('it can bulk delete service requests as a team manager', function () {
    $user = User::factory()->create();

    $user->givePermissionTo('service_request.view-any');
    $user->givePermissionTo('service_request.*.delete');

    $team = Team::factory()->create();
    $user->team()->associate($team)->save();
    $user->refresh();

    $serviceRequestType = ServiceRequestType::factory()->create();
    $serviceRequestType->managerTeams()->attach($team);

    $serviceRequestPriority = ServiceRequestPriority::factory()
        ->for($serviceRequestType, 'type')
        ->create();

    $serviceRequests = ServiceRequest::factory()
        ->for($serviceRequestPriority, 'priority')
        ->count(3)
        ->create();

    actingAs($user);

    livewire(ListServiceRequests::class)
        ->assertSuccessful()
        ->callTableBulkAction('delete', $serviceRequests);

    foreach ($serviceRequests as $sr) {
        expect(ServiceRequest::find($sr->getKey()))->toBeNull();
    }
});

test('it can bulk delete service requests as a direct user manager', function () {
    $user = User::factory()->create();

    $user->givePermissionTo('service_request.view-any');
    $user->givePermissionTo('service_request.*.delete');

    $serviceRequestType = ServiceRequestType::factory()->create();
    $serviceRequestType->managerUsers()->attach($user);

    $serviceRequestPriority = ServiceRequestPriority::factory()
        ->for($serviceRequestType, 'type')
        ->create();

    $serviceRequests = ServiceRequest::factory()
        ->for($serviceRequestPriority, 'priority')
        ->count(3)
        ->create();

    actingAs($user);

    livewire(ListServiceRequests::class)
        ->assertSuccessful()
        ->callTableBulkAction('delete', $serviceRequests);

    foreach ($serviceRequests as $sr) {
        expect(ServiceRequest::find($sr->getKey()))->toBeNull();
    }
});

test('it cannot bulk delete service requests the user does not manage', function () {
    $user = User::factory()->create();

    $user->givePermissionTo('service_request.view-any');
    $user->givePermissionTo('service_request.*.delete');

    actingAs($user);

    $serviceRequests = ServiceRequest::factory()
        ->count(3)
        ->create();

    livewire(ListServiceRequests::class)
        ->assertSuccessful()
        ->callTableBulkAction('delete', $serviceRequests);

    foreach ($serviceRequests as $sr) {
        expect(ServiceRequest::find($sr->getKey()))->not->toBeNull();
    }
});
