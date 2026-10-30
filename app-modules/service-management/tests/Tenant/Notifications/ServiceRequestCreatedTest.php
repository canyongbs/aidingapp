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

use AidingApp\Contact\Filament\Resources\ContactResource;
use AidingApp\Contact\Models\Contact;
use AidingApp\Contact\Models\Organization;
use AidingApp\Notification\Notifications\Channels\DatabaseChannel;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequests\ServiceRequestResource;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\ServiceManagement\Notifications\ServiceRequestCreated;
use App\Models\User;

it('builds the database notification title with a linked service request number, type name, and linked customer', function (): void {
    $type = ServiceRequestType::factory()->create(['name' => 'Technical Support']);
    $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
    $organization = Organization::factory()->create(['name' => 'Acme Inc']);
    $contact = Contact::factory()->for($organization, 'organization')->create(['full_name' => 'Jane Doe']);
    $serviceRequest = ServiceRequest::factory()->for($priority, 'priority')->for($contact, 'respondent')->create();

    $title = (new ServiceRequestCreated($serviceRequest, null, DatabaseChannel::class))
        ->toDatabase(User::factory()->create())['title'];

    $serviceRequestUrl = ServiceRequestResource::getUrl('view', ['record' => $serviceRequest]);
    $contactUrl = ContactResource::getUrl('view', ['record' => $contact]);

    expect($title)->toBe(
        "Service request <a href='{$serviceRequestUrl}' target='_blank' class='underline'>{$serviceRequest->service_request_number}</a> (Technical Support) has been created for <a href='{$contactUrl}' target='_blank' class='underline'>Jane Doe (Acme Inc)</a>."
    );
});

it('shows only the contact name when the customer has no organization', function (): void {
    $type = ServiceRequestType::factory()->create();
    $priority = ServiceRequestPriority::factory()->for($type, 'type')->create();
    $contact = Contact::factory()->create(['full_name' => 'Jane Doe']);
    $serviceRequest = ServiceRequest::factory()->for($priority, 'priority')->for($contact, 'respondent')->create();

    $title = (new ServiceRequestCreated($serviceRequest, null, DatabaseChannel::class))
        ->toDatabase(User::factory()->create())['title'];

    $contactUrl = ContactResource::getUrl('view', ['record' => $contact]);

    expect($title)->toContain("<a href='{$contactUrl}' target='_blank' class='underline'>Jane Doe</a>.")
        ->and($title)->not->toContain('Jane Doe (');
});
