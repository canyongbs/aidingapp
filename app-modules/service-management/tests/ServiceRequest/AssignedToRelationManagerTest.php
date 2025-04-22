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
use AidingApp\ServiceManagement\Enums\SystemServiceRequestClassification;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestResource\Pages\ManageAssignments;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestResource\RelationManagers\AssignedToRelationManager;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestAssignment;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\Team\Models\Team;
use App\Models\User;
use App\Settings\LicenseSettings;
use Filament\Forms\Components\Select;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

test('During assignment only Users that are on a Team that is Manager of the Type of this Service Request should be options', function () {
    asSuperAdmin();

    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    $user = User::factory()->licensed([Contact::getLicenseType()])->create();
    $userWithoutTeam = User::factory()->licensed([Contact::getLicenseType()])->create();

    $team = Team::factory()->create();

    $user->team()->associate($team);

    $user->refresh();

    $serviceRequestType = ServiceRequestType::factory()->create();

    $serviceRequestType->managers()->attach($team);

    $serviceRequestsWithManager = ServiceRequest::factory()->state([
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestType->getKey(),
        ])->getKey(),
    ])
        ->create();

    livewire(AssignedToRelationManager::class, [
        'ownerRecord' => $serviceRequestsWithManager,
        'pageClass' => ManageAssignments::class,
    ])
        ->mountTableAction('assign-service-request')
        ->assertFormFieldExists('userId', 'mountedTableActionForm', function (Select $select) use ($user) {
            $options = $select->getSearchResults($user->name);

            return ! empty($options);
        })
        ->assertSuccessful()
        ->assertFormFieldExists('userId', 'mountedTableActionForm', function (Select $select) use ($userWithoutTeam) {
            $options = $select->getSearchResults($userWithoutTeam->name);

            return empty($options);
        })
        ->assertSuccessful();
});

test('During reassignment current assigned user should not be in options', function () {
    asSuperAdmin();

    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    $user = User::factory()->licensed([Contact::getLicenseType()])->create();
    $secondUser = User::factory()->licensed([Contact::getLicenseType()])->create();

    $team = Team::factory()->create();

    $user->team()->associate($team);

    $secondUser->team()->associate($team);

    $user->refresh();

    $serviceRequestType = ServiceRequestType::factory()->create();

    $serviceRequestType->managers()->attach($team);

    $serviceRequestsWithManager = ServiceRequest::factory()->state([
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestType->getKey(),
        ])->getKey(),
    ])
        ->create();

    $assignedServiceRequest = ServiceRequestAssignment::factory()->state([
        'service_request_id' => $serviceRequestsWithManager->getKey(),
        'user_id' => $user->getKey(),
    ])
        ->create();

    livewire(AssignedToRelationManager::class, [
        'ownerRecord' => $serviceRequestsWithManager,
        'pageClass' => ManageAssignments::class,
    ])
        ->mountTableAction('assign-service-request')
        ->assertFormFieldExists('userId', 'mountedTableActionForm', function (Select $select) use ($user) {
            $options = $select->getSearchResults($user->name);

            return empty($options);
        })
        ->assertSuccessful()
        ->assertFormFieldExists('userId', 'mountedTableActionForm', function (Select $select) use ($secondUser) {
            $options = $select->getSearchResults($secondUser->name);

            return ! empty($options);
        })
        ->assertSuccessful();
});

test('Assign To Me action visible when the Service Request is unassigned and the logged-in user belongs to a Team that is Manager of the Type of this Service Request', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    $user = User::factory()->licensed([Contact::getLicenseType()])->create();

    $user->givePermissionTo('service_request.*.update');

    $team = Team::factory()->create();

    $user->team()->associate($team);

    $user->refresh();

    $serviceRequestType = ServiceRequestType::factory()->create();

    $serviceRequestType->managers()->associate($team);

    actingAs($user);

    $serviceRequestsWithManager = ServiceRequest::factory()->state([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Open,
        ])->getKey(),
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestType->getKey(),
        ])->getKey(),
    ])
        ->create();

    livewire(AssignedToRelationManager::class, [
        'ownerRecord' => $serviceRequestsWithManager,
        'pageClass' => ManageAssignments::class,
    ])
        ->assertTableActionVisible('assign-to-me');
});

test('Assign To Me action is not visible when the Service Request is already assigned and the logged-in user belongs to a Team that is Manager of the Type of this Service Request', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    $user = User::factory()->licensed([Contact::getLicenseType()])->create();

    $user->givePermissionTo('service_request.*.update');

    actingAs($user);

    $team = Team::factory()->create();

    $user->team()->associate($team);

    $user->refresh();

    $serviceRequestType = ServiceRequestType::factory()->create();

    $serviceRequestType->managers()->attach($team);

    $serviceRequestsWithManager = ServiceRequest::factory()->state([
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestType->getKey(),
        ])->getKey(),
    ])
        ->create();

    $assignedServiceRequest = ServiceRequestAssignment::factory()->state([
        'service_request_id' => $serviceRequestsWithManager->getKey(),
        'user_id' => $user->getKey(),
    ])
        ->create();

    livewire(AssignedToRelationManager::class, [
        'ownerRecord' => $serviceRequestsWithManager,
        'pageClass' => ManageAssignments::class,
    ])
        ->assertTableActionHidden('assign-to-me');
});

test('Assign To Me action is not visible when the Service Request is unassigned and the logged-in user not belongs to a Team that is Manager of the Type of this Service Request', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    $user = User::factory()->licensed([Contact::getLicenseType()])->create();

    $team = Team::factory()->create();

    $serviceRequestType = ServiceRequestType::factory()->create();

    $serviceRequestType->managers()->attach($team);

    actingAs($user);

    $serviceRequestsWithManager = ServiceRequest::factory()->state([
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestType->getKey(),
        ])->getKey(),
    ])
        ->create();

    livewire(AssignedToRelationManager::class, [
        'ownerRecord' => $serviceRequestsWithManager,
        'pageClass' => ManageAssignments::class,
    ])
        ->assertTableActionHidden('assign-to-me');
});

test('Assign Service Request action visible when the Service Request is unassigned and the logged-in user belongs to a Team that is Manager of the Type of this Service Request', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    $user = User::factory()->licensed([Contact::getLicenseType()])->create();

    $user->givePermissionTo('service_request.*.update');

    actingAs($user);

    $team = Team::factory()->create();

    $user->team()->associate($team);

    $user->refresh();

    $serviceRequestType = ServiceRequestType::factory()->create();

    $serviceRequestType->managers()->attach($team);

    $serviceRequestsWithManager = ServiceRequest::factory()->state([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Open,
        ])->getKey(),
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestType->getKey(),
        ])->getKey(),
    ])
        ->create();

    livewire(AssignedToRelationManager::class, [
        'ownerRecord' => $serviceRequestsWithManager,
        'pageClass' => ManageAssignments::class,
    ])
        ->assertTableActionVisible('assign-service-request');
});

test('Assign Service Request action is not visible when the Service Request is unassigned and the logged-in user not belongs to a Team that is Manager of the Type of this Service Request', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    $user = User::factory()->licensed([Contact::getLicenseType()])->create();

    $team = Team::factory()->create();

    $serviceRequestType = ServiceRequestType::factory()->create();

    $serviceRequestType->managers()->attach($team);

    actingAs($user);

    $serviceRequestsWithManager = ServiceRequest::factory()->state([
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestType->getKey(),
        ])->getKey(),
    ])
        ->create();

    livewire(AssignedToRelationManager::class, [
        'ownerRecord' => $serviceRequestsWithManager,
        'pageClass' => ManageAssignments::class,
    ])
        ->assertTableActionHidden('assign-service-request');
});

test('Assign To Me action is not visible when the Service Request is Closed and the logged-in user belongs a Team that is Manager of the Type of this Service Request', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    $user = User::factory()->licensed([Contact::getLicenseType()])->create();

    $user->givePermissionTo('service_request.*.update');

    actingAs($user);

    $team = Team::factory()->create();

    $user->team()->associate($team);

    $user->refresh();

    $serviceRequestType = ServiceRequestType::factory()->create();

    $serviceRequestType->managers()->attach($team);

    $serviceRequestsWithManager = ServiceRequest::factory()->state([
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestType->getKey(),
        ])->getKey(),
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Closed,
        ])->getKey(),
    ])
        ->create();

    livewire(AssignedToRelationManager::class, [
        'ownerRecord' => $serviceRequestsWithManager,
        'pageClass' => ManageAssignments::class,
    ])
        ->assertTableActionHidden('assign-to-me');
});

test('Assign Service Request action is not visible when the Service Request is Closed and the logged-in user belongs to a Team that is Manager of the Type of this Service Request', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    $user = User::factory()->licensed([Contact::getLicenseType()])->create();

    $team = Team::factory()->create();

    $user->team()->associate($team);

    $user->refresh();

    $serviceRequestType = ServiceRequestType::factory()->create();

    $serviceRequestType->managers()->attach($team);

    actingAs($user);

    $serviceRequestsWithManager = ServiceRequest::factory()->state([
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestType->getKey(),
        ])->getKey(),
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Closed,
        ])->getKey(),
    ])
        ->create();

    livewire(AssignedToRelationManager::class, [
        'ownerRecord' => $serviceRequestsWithManager,
        'pageClass' => ManageAssignments::class,
    ])
        ->assertTableActionHidden('assign-service-request');
});
