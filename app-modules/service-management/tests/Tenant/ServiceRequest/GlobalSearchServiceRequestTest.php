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

use AidingApp\Contact\Models\Contact;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestResource;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use AidingApp\Team\Models\Team;
use App\Models\User;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Tests\asSuperAdmin;

test('global search is gated with proper feature access control', function () {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = true;
    $settings->save();

    $user = User::factory()->create();
    $team = Team::factory()->create();
    $user->team()->associate($team)->save();
    $user->refresh();

    $serviceRequestType = ServiceRequestType::factory()->create();
    $serviceRequestType->managers()->attach($team);

    $priority = ServiceRequestPriority::factory()->create([
        'type_id' => $serviceRequestType->id,
    ]);

    ServiceRequest::factory()->create([
        'priority_id' => $priority->id,
        'service_request_number' => 'SR-FEATURE',
    ]);

    actingAs($user);

    $query = ServiceRequestResource::getGlobalSearchEloquentQuery();
    $results = $query->get();

    expect($results)->toHaveCount(1);
    expect($results->first()->service_request_number)->toBe('SR-FEATURE');
});

test('global search returns service requests for managers of service request type', function () {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = true;
    $settings->save();

    $user = User::factory()->create();
    $team = Team::factory()->create();
    $user->team()->associate($team)->save();
    $user->refresh();

    $serviceRequestType = ServiceRequestType::factory()->create();
    $serviceRequestType->managers()->attach($team);

    $priority = ServiceRequestPriority::factory()->create([
        'type_id' => $serviceRequestType->id,
    ]);

    $serviceRequest = ServiceRequest::factory()->create([
        'priority_id' => $priority->id,
    ]);

    actingAs($user);

    $query = ServiceRequestResource::getGlobalSearchEloquentQuery();
    $results = $query->get();

    expect($results)->toHaveCount(1);
    expect($results->first()->service_request_number)->toBe($serviceRequest->service_request_number);
});

test('global search returns service requests for auditors of service request type', function () {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = true;
    $settings->save();

    $user = User::factory()->create();
    $team = Team::factory()->create();
    $user->team()->associate($team)->save();
    $user->refresh();

    $serviceRequestType = ServiceRequestType::factory()->create();
    $serviceRequestType->auditors()->attach($team);

    $priority = ServiceRequestPriority::factory()->create([
        'type_id' => $serviceRequestType->id,
    ]);

    $serviceRequest = ServiceRequest::factory()->create([
        'priority_id' => $priority->id,
    ]);

    actingAs($user);

    $query = ServiceRequestResource::getGlobalSearchEloquentQuery();
    $results = $query->get();

    expect($results)->toHaveCount(1);
    expect($results->first()->service_request_number)->toBe($serviceRequest->service_request_number);
});

test('global search does not return service requests for non-managers/auditors', function () {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = true;
    $settings->save();

    $user = User::factory()->create();
    $team = Team::factory()->create();
    $user->team()->associate($team)->save();
    $user->refresh();

    $serviceRequestType = ServiceRequestType::factory()->create();

    $priority = ServiceRequestPriority::factory()->create([
        'type_id' => $serviceRequestType->id,
    ]);

    ServiceRequest::factory()->create([
        'priority_id' => $priority->id,
    ]);

    actingAs($user);

    $query = ServiceRequestResource::getGlobalSearchEloquentQuery();
    $results = $query->get();

    expect($results)->toHaveCount(0);
});

test('global search returns all service requests for super admin', function () {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = true;
    $settings->save();

    $superAdmin = User::factory()->create();
    asSuperAdmin($superAdmin);

    $serviceRequestType = ServiceRequestType::factory()->create();
    $priority = ServiceRequestPriority::factory()->create([
        'type_id' => $serviceRequestType->id,
    ]);

    ServiceRequest::factory()->create([
        'priority_id' => $priority->id,
    ]);

    ServiceRequest::factory()->create([
        'priority_id' => $priority->id,
    ]);

    actingAs($superAdmin);

    $query = ServiceRequestResource::getGlobalSearchEloquentQuery();
    $results = $query->get();

    expect($results)->toHaveCount(2);
});

test('global search returns no results for user without team', function () {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = true;
    $settings->save();

    $user = User::factory()->create();

    $serviceRequestType = ServiceRequestType::factory()->create();
    $priority = ServiceRequestPriority::factory()->create([
        'type_id' => $serviceRequestType->id,
    ]);

    ServiceRequest::factory()->create([
        'priority_id' => $priority->id,
    ]);

    actingAs($user);

    $query = ServiceRequestResource::getGlobalSearchEloquentQuery();
    $results = $query->get();

    expect($results)->toHaveCount(0);
});

test('global search details are correctly formatted', function () {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = true;
    $settings->save();

    $user = User::factory()->create();
    $team = Team::factory()->create();
    $user->team()->associate($team)->save();
    $user->refresh();

    $serviceRequestType = ServiceRequestType::factory()->create(['name' => 'Test Type']);
    $serviceRequestType->managers()->attach($team);

    $priority = ServiceRequestPriority::factory()->create([
        'name' => 'High Priority',
        'type_id' => $serviceRequestType->id,
    ]);

    $status = ServiceRequestStatus::factory()->create(['name' => 'Open']);
    $contact = Contact::factory()->create();

    $serviceRequest = ServiceRequest::factory()->create([
        'priority_id' => $priority->id,
        'status_id' => $status->id,
        'respondent_id' => $contact->id,
    ]);

    actingAs($user);

    $details = ServiceRequestResource::getGlobalSearchResultDetails($serviceRequest);

    expect($details)->toHaveKey('Status');
    expect($details)->toHaveKey('Type');
    expect($details)->toHaveKey('Priority');
    expect($details)->toHaveKey('Related To');

    expect($details['Type'])->toBe('Test Type');
    expect($details['Priority'])->toBe('High Priority');
    expect($details['Status'])->toBe('Open');
});

test('global search URL points to view page', function () {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = true;
    $settings->save();

    $user = User::factory()->create();
    $team = Team::factory()->create();
    $user->team()->associate($team)->save();
    $user->refresh();

    $serviceRequestType = ServiceRequestType::factory()->create();
    $serviceRequestType->managers()->attach($team);

    $priority = ServiceRequestPriority::factory()->create([
        'type_id' => $serviceRequestType->id,
    ]);

    $serviceRequest = ServiceRequest::factory()->create([
        'priority_id' => $priority->id,
    ]);

    actingAs($user);

    $url = ServiceRequestResource::getGlobalSearchResultUrl($serviceRequest);

    expect($url)->toContain('/service-requests/' . $serviceRequest->getKey());
    expect($url)->not->toContain('/edit');
});

test('globally searchable attributes include service request number and title', function () {
    $attributes = ServiceRequestResource::getGloballySearchableAttributes();

    expect($attributes)->toContain('service_request_number');
    expect($attributes)->toContain('title');
});
