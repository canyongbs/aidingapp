<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Aiding App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Aiding App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use AidingApp\Contact\Models\Contact;
use AidingApp\Department\Models\Department;
use AidingApp\Engagement\Models\Engagement;
use AidingApp\Engagement\Models\EngagementFile;
use AidingApp\InventoryManagement\Models\Asset;
use AidingApp\InventoryManagement\Models\AssetCheckIn;
use AidingApp\InventoryManagement\Models\AssetCheckOut;
use AidingApp\ServiceManagement\Enums\SystemServiceRequestClassification;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequests\Pages\ViewServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use App\Models\User;
use Filament\Actions\Testing\TestAction;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

// A user who can reach the ViewServiceRequest page must always have `service_request.view-any`,
// which is also the exact ability that gates the Service Requests tab - so give tests full
// control over every OTHER tab's permission while keeping page access constant.
function actingAsServiceRequestViewer(string ...$extraPermissions): ServiceRequest
{
    $user = User::factory()->create();

    $department = Department::factory()->create();
    $user->department()->associate($department)->save();
    $user->refresh();

    $serviceRequestType = ServiceRequestType::factory()->create();
    $serviceRequestType->managerDepartments()->attach($department);

    $serviceRequest = ServiceRequest::factory()->state([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Open,
        ])->getKey(),
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestType->getKey(),
        ])->getKey(),
    ])->create();

    $user->givePermissionTo('service_request.view-any', 'service_request.*.view', 'contact.*.view', ...$extraPermissions);

    actingAs($user->refresh());

    return $serviceRequest;
}

function mountViewContact(ServiceRequest $serviceRequest)
{
    return livewire(ViewServiceRequest::class, [
        'record' => $serviceRequest->getRouteKey(),
    ])
        ->mountAction(TestAction::make('viewContact')->schemaComponent('respondent'))
        ->assertActionMounted(TestAction::make('viewContact')->schemaComponent('respondent'));
}

// Authorization - viewContact action itself

test('viewContact action is visible for a user with permission to view contacts', function () {
    $serviceRequest = actingAsServiceRequestViewer();

    livewire(ViewServiceRequest::class, [
        'record' => $serviceRequest->getRouteKey(),
    ])
        ->assertSuccessful()
        ->assertActionVisible(TestAction::make('viewContact')->schemaComponent('respondent'));
});

test('viewContact action is hidden for a user without permission to view contacts', function () {
    $user = User::factory()->create();

    $department = Department::factory()->create();
    $user->department()->associate($department)->save();
    $user->refresh();

    $serviceRequestType = ServiceRequestType::factory()->create();
    $serviceRequestType->managerDepartments()->attach($department);

    $serviceRequest = ServiceRequest::factory()->state([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Open,
        ])->getKey(),
        'priority_id' => ServiceRequestPriority::factory()->create([
            'type_id' => $serviceRequestType->getKey(),
        ])->getKey(),
    ])->create();

    $user->givePermissionTo('service_request.view-any', 'service_request.*.view');

    actingAs($user->refresh());

    livewire(ViewServiceRequest::class, [
        'record' => $serviceRequest->getRouteKey(),
    ])
        ->assertSuccessful()
        ->assertActionHidden(TestAction::make('viewContact')->schemaComponent('respondent'));
});

// Service Requests tab (always visible to anyone who can reach this page - see actingAsServiceRequestViewer())

test('viewContact shows scoped service request records on the Service Requests tab', function () {
    $serviceRequest = actingAsServiceRequestViewer();

    $otherServiceRequest = ServiceRequest::factory()->create([
        'respondent_id' => $serviceRequest->respondent_id,
        'priority_id' => $serviceRequest->priority_id,
    ]);

    mountViewContact($serviceRequest)
        ->assertMountedActionModalSee('Service Requests')
        ->assertMountedActionModalSee($otherServiceRequest->service_request_number);
});

// Assets tab - Checked Out Assets

test('viewContact shows scoped checked out assets when the user has permission', function () {
    $serviceRequest = actingAsServiceRequestViewer('asset_check_out.view-any');

    $asset = Asset::factory()->create(['name' => 'Checked Out Test Laptop']);
    AssetCheckOut::factory()->create([
        'asset_id' => $asset->getKey(),
        'checked_out_to_id' => $serviceRequest->respondent_id,
    ]);

    mountViewContact($serviceRequest)
        ->assertMountedActionModalSee('Checked Out Assets')
        ->assertMountedActionModalSee('Checked Out Test Laptop');
});

test('viewContact hides the Checked Out Assets section for a user without permission', function () {
    $serviceRequest = actingAsServiceRequestViewer();

    $asset = Asset::factory()->create(['name' => 'Checked Out Test Laptop']);
    AssetCheckOut::factory()->create([
        'asset_id' => $asset->getKey(),
        'checked_out_to_id' => $serviceRequest->respondent_id,
    ]);

    mountViewContact($serviceRequest)
        ->assertMountedActionModalDontSee('Checked Out Assets')
        ->assertMountedActionModalDontSee('Checked Out Test Laptop');
});

// Assets tab - Returned Assets

test('viewContact shows scoped returned assets when the user has permission', function () {
    $serviceRequest = actingAsServiceRequestViewer('asset_check_in.view-any');

    $asset = Asset::factory()->create(['name' => 'Returned Test Monitor']);
    AssetCheckOut::factory()->create(['asset_id' => $asset->getKey()]);
    AssetCheckIn::factory()->create([
        'asset_id' => $asset->getKey(),
        'checked_in_from_id' => $serviceRequest->respondent_id,
    ]);

    mountViewContact($serviceRequest)
        ->assertMountedActionModalSee('Returned Assets')
        ->assertMountedActionModalSee('Returned Test Monitor');
});

test('viewContact hides the Returned Assets section for a user without permission', function () {
    $serviceRequest = actingAsServiceRequestViewer();

    $asset = Asset::factory()->create(['name' => 'Returned Test Monitor']);
    AssetCheckOut::factory()->create(['asset_id' => $asset->getKey()]);
    AssetCheckIn::factory()->create([
        'asset_id' => $asset->getKey(),
        'checked_in_from_id' => $serviceRequest->respondent_id,
    ]);

    mountViewContact($serviceRequest)
        ->assertMountedActionModalDontSee('Returned Assets')
        ->assertMountedActionModalDontSee('Returned Test Monitor');
});

// Files tab

test('viewContact shows scoped engagement files when the user has permission', function () {
    $serviceRequest = actingAsServiceRequestViewer('engagement_file.view-any');

    $contact = $serviceRequest->respondent;
    assert($contact instanceof Contact);

    $engagementFile = EngagementFile::factory()->create(['description' => 'Scoped Test File Description']);
    $contact->engagementFiles()->attach($engagementFile);

    mountViewContact($serviceRequest)
        ->assertMountedActionModalSee('Files')
        ->assertMountedActionModalSee('Scoped Test File Description');
});

test('viewContact hides the Files tab for a user without permission', function () {
    $serviceRequest = actingAsServiceRequestViewer();

    $contact = $serviceRequest->respondent;
    assert($contact instanceof Contact);

    $engagementFile = EngagementFile::factory()->create(['description' => 'Scoped Test File Description']);
    $contact->engagementFiles()->attach($engagementFile);

    mountViewContact($serviceRequest)
        ->assertMountedActionModalDontSee('Files')
        ->assertMountedActionModalDontSee('Scoped Test File Description');
});

// Emails tab

test('viewContact shows scoped engagement timeline entries when the user has permission', function () {
    $serviceRequest = actingAsServiceRequestViewer('engagement.view-any');

    $contact = $serviceRequest->respondent;
    assert($contact instanceof Contact);

    Engagement::factory()->create([
        'recipient_type' => $contact->getMorphClass(),
        'recipient_id' => $contact->getKey(),
    ]);

    mountViewContact($serviceRequest)
        ->assertMountedActionModalSee('Emails')
        ->assertMountedActionModalSee('Outbound');
});

test('viewContact hides the Emails tab for a user without permission', function () {
    $serviceRequest = actingAsServiceRequestViewer();

    $contact = $serviceRequest->respondent;
    assert($contact instanceof Contact);

    Engagement::factory()->create([
        'recipient_type' => $contact->getMorphClass(),
        'recipient_id' => $contact->getKey(),
    ]);

    mountViewContact($serviceRequest)
        ->assertMountedActionModalDontSee('Emails')
        ->assertMountedActionModalDontSee('Outbound');
});

// Email health

test('viewContact shows the email health callout for a bounced respondent contact', function () {
    $serviceRequest = actingAsServiceRequestViewer();

    $contact = $serviceRequest->respondent;
    assert($contact instanceof Contact);
    $contact->update(['email_bounce' => true]);

    mountViewContact($serviceRequest)
        ->assertSchemaComponentVisible('email-health-callout', 'mountedActionSchema0');
});

test('viewContact hides the email health callout for a healthy respondent contact', function () {
    $serviceRequest = actingAsServiceRequestViewer();

    $contact = $serviceRequest->respondent;
    assert($contact instanceof Contact);
    $contact->update(['email_bounce' => false]);

    mountViewContact($serviceRequest)
        ->assertSchemaComponentHidden('email-health-callout', 'mountedActionSchema0');
});

// Success - full contact details

test('viewContact shows the full contact details for the service request respondent', function () {
    $serviceRequest = ServiceRequest::factory()->state([
        'status_id' => ServiceRequestStatus::factory()->create([
            'classification' => SystemServiceRequestClassification::Open,
        ])->getKey(),
    ])->create();

    $contact = $serviceRequest->respondent;

    assert($contact instanceof Contact);

    asSuperAdmin();

    mountViewContact($serviceRequest)
        ->assertActionDataSet([
            'first_name' => $contact->first_name,
            'last_name' => $contact->last_name,
            'email' => $contact->email,
        ])
        ->assertMountedActionModalSee($contact->{Contact::displayNameKey()})
        ->assertMountedActionModalSee('Demographic Information')
        ->assertMountedActionModalSee('Go to Contact');
});
