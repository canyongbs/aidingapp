<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

use App\Models\User;
use AidingApp\Team\Models\Team;

use function Tests\asSuperAdmin;

use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use AidingApp\Contact\Models\Contact;
use AidingApp\Contact\Models\Organization;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\Contact\Filament\Resources\ContactResource;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestAssignment;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestResource;
use AidingApp\Contact\Filament\Resources\ContactResource\Pages\ContactServiceManagement;
use AidingApp\Contact\Filament\Resources\ContactResource\RelationManagers\ServiceRequestsRelationManager;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestResource\Pages\ListServiceRequests;

test('The correct details are displayed on the ListServiceRequests page', function () {
    $serviceRequests = ServiceRequest::factory()
        ->has(
            factory: ServiceRequestAssignment::factory()
                ->count(1)
                ->active(),
            relationship: 'assignments'
        )
        ->count(10)
        ->create();

    asSuperAdmin();

    $component = livewire(ListServiceRequests::class);

    $component->assertSuccessful()
        ->assertCanSeeTableRecords($serviceRequests)
        ->assertCountTableRecords(10);

    $serviceRequests->each(
        fn (ServiceRequest $serviceRequest) => $component
            ->assertTableColumnStateSet(
                'service_request_number',
                $serviceRequest->service_request_number,
                $serviceRequest
            )
            ->assertTableColumnStateSet(
                'respondent.display_name',
                $serviceRequest->respondent->full_name,
                $serviceRequest
            )
            ->assertTableColumnStateSet(
                'division.name',
                $serviceRequest->division->name,
                $serviceRequest
            )
            ->assertTableColumnStateSet(
                'assignedTo.user.name',
                $serviceRequest->assignedTo->user->name,
                $serviceRequest
            )
    );
});

// TODO: Sorting and Searching tests

// Permission Tests

test('ListServiceRequests is gated with proper access control', function () {
    $user = User::factory()->licensed([Contact::getLicenseType()])->create();

    actingAs($user)
        ->get(
            ServiceRequestResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('service_request.view-any');

    actingAs($user)
        ->get(
            ServiceRequestResource::getUrl('index')
        )->assertSuccessful();
});

test('ListServiceRequests is gated with proper feature access control', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = false;

    $settings->save();

    $user = User::factory()->licensed([Contact::getLicenseType()])->create();

    $user->givePermissionTo('service_request.view-any');

    actingAs($user)
        ->get(
            ServiceRequestResource::getUrl()
        )->assertForbidden();

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    actingAs($user)
        ->get(
            ServiceRequestResource::getUrl()
        )->assertSuccessful();
});

test('can filter service request by organization', function () {
    asSuperAdmin();

    $organizations = Organization::factory()->count(10)->create();

    $organization = $organizations->first();

    $serviceRequestsInOrganization = ServiceRequest::factory()
        ->count(3)
        ->for(Contact::factory()->state(['organization_id' => $organization->id]), 'respondent')
        ->create();

    $serviceRequestsNotInOrganization = ServiceRequest::factory()
        ->count(3)
        ->create();

    livewire(ListServiceRequests::class)
        ->assertCanSeeTableRecords($serviceRequestsInOrganization->merge($serviceRequestsNotInOrganization))
        ->filterTable('organizations', $organization->id)
        ->assertCanSeeTableRecords(
            $serviceRequestsInOrganization
        )
        ->assertCanNotSeeTableRecords($serviceRequestsNotInOrganization);
});

test('service requests only visible to service request type managers', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    $user = User::factory()->licensed([Contact::getLicenseType()])->create();

    $user->givePermissionTo('service_request.view-any');

    $team = Team::factory()->create();

    $user->teams()->attach($team);

    $user->refresh();

    actingAs($user);

    $serviceRequests = ServiceRequest::factory()
        ->count(3)
        ->create();

    $serviceRequestType = ServiceRequestType::factory()->create();

    $serviceRequestType->managers()->attach($team);

    $serviceRequestsWithManager = ServiceRequest::factory()->state([
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestType->getKey(),
        ])->getKey(),
    ])
        ->count(3)
        ->create();

    livewire(ListServiceRequests::class)
        ->assertCanSeeTableRecords(
            $serviceRequestsWithManager
        )
        ->assertCanNotSeeTableRecords($serviceRequests);
});

test('service requests only visible to service request type auditors', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    $user = User::factory()->licensed([Contact::getLicenseType()])->create();

    $user->givePermissionTo('service_request.view-any');

    $team = Team::factory()->create();

    $user->teams()->attach($team);

    $user->refresh();

    actingAs($user);

    $serviceRequests = ServiceRequest::factory()
        ->count(3)
        ->create();

    $serviceRequestType = ServiceRequestType::factory()->create();

    $serviceRequestType->auditors()->attach($team);

    $serviceRequestsWithAuditors = ServiceRequest::factory()->state([
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestType->getKey(),
        ])->getKey(),
    ])
        ->count(3)
        ->create();

    livewire(ListServiceRequests::class)
        ->assertCanSeeTableRecords(
            $serviceRequestsWithAuditors
        )
        ->assertCanNotSeeTableRecords($serviceRequests);
});

test('can list audit member to service request type', function () {

    $user = User::factory()->licensed([Contact::getLicenseType()])->create();
    $team = Team::factory()->create();
    $user->teams()->attach($team);
    $user->refresh();

    $contact = Contact::factory()->create();

    $serviceRequestsWithoutManager = ServiceRequest::factory()
                                    ->for($contact, 'respondent')
                                    ->count(3)
                                    ->create();

    $serviceRequests = ServiceRequest::factory()->state([
        'priority_id' => ServiceRequestPriority::factory()->for(ServiceRequestType::factory()
        ->hasAttached($team,[],'managers'),'type'),
    ])
    ->for($contact, 'respondent')
    ->count(3)
    ->create();

    actingAs($user)
        ->get(
            ContactResource::getUrl('service-management', [
                'record' => $contact->getRouteKey(),
            ])
        )->assertForbidden();

    $user->givePermissionTo('service_request.view-any');
    $user->givePermissionTo('service_request.create');
    $user->givePermissionTo('team.view-any');
    $user->givePermissionTo('contact.view-any');

    actingAs($user);

    livewire(ServiceRequestsRelationManager::class, [
        'ownerRecord' => $contact,
        'pageClass' => ContactServiceManagement::class,
    ])
    ->assertCanSeeTableRecords($serviceRequests)
    ->assertCanNotSeeTableRecords($serviceRequestsWithoutManager);
});
