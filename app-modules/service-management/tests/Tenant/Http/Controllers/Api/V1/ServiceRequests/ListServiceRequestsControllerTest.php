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
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestAssignment;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use App\Models\SystemUser;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\getJson;

beforeEach(function () {
    // Disable auditing, which causes testing issues when authenticating with a fake Sanctum token.
    config()->set('audit.enabled', false);
});

it('is gated with proper access control', function () {
    $user = SystemUser::factory()->create();
    Sanctum::actingAs($user, ['api']);
    getJson(route('api.v1.service-requests.index', [], false))
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('service_request.view-any');
    Sanctum::actingAs($user, ['api']);
    getJson(route('api.v1.service-requests.index', [], false))
        ->assertOk();
});

it('returns a paginated list of service requests', function () {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('service_request.view-any');
    Sanctum::actingAs($user, ['api']);

    ServiceRequest::factory()->count(3)->create();

    $response = getJson(route('api.v1.service-requests.index', [], false));
    $response->assertOk();
    $response->assertJsonStructure([
        'data',
        'links',
        'meta',
    ]);

    expect($response['data'])
        ->toHaveCount(3);
});

it('can filter service requests by title', function () {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('service_request.view-any');
    Sanctum::actingAs($user, ['api']);

    ServiceRequest::factory()->create(['title' => 'Specific Request Title']);
    ServiceRequest::factory()->count(2)->create(['title' => 'Other Title']);

    $response = getJson(route('api.v1.service-requests.index', ['filter' => ['title' => 'Specific Request']], false));
    $response->assertOk();

    expect($response['data'][0]['title'])
        ->toBe('Specific Request Title');
    expect($response['meta']['total'])
        ->toBe(1);
});

it('can filter service requests by status', function () {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('service_request.view-any');
    Sanctum::actingAs($user, ['api']);

    $status = ServiceRequestStatus::factory()->create();
    $status2 = ServiceRequestStatus::factory()->create();
    ServiceRequest::factory()->create(['status_id' => $status->id]);
    ServiceRequest::factory()->create(['status_id' => $status2->id]);

    $response = getJson(route('api.v1.service-requests.index', ['filter' => ['status' => $status->id]], false));
    $response->assertOk();

    expect($response['data'][0]['status']['id'])
        ->toBe($status->id);
    expect($response['meta']['total'])
        ->toBe(1);
});

it('can filter service requests by requestor', function () {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('service_request.view-any');
    Sanctum::actingAs($user, ['api']);

    $contact = Contact::factory()->create();
    ServiceRequest::factory()->create(['respondent_id' => $contact->id]);
    ServiceRequest::factory()->count(2)->create();

    $response = getJson(route('api.v1.service-requests.index', ['filter' => ['requestor' => $contact->id]], false));
    $response->assertOk();

    expect($response['meta']['total'])
        ->toBe(1);
});

it('can filter service requests by assignee', function () {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('service_request.view-any');
    Sanctum::actingAs($user, ['api']);

    $assigneeUser = User::factory()->create();
    $serviceRequest = ServiceRequest::factory()->create();
    $serviceRequest->priority->type->managerUsers()->attach($assigneeUser);

    ServiceRequestAssignment::factory()->create([
        'service_request_id' => $serviceRequest->id,
        'user_id' => $assigneeUser->id,
    ]);
    ServiceRequest::factory()->count(2)->create();

    $response = getJson(route('api.v1.service-requests.index', ['filter' => ['assignee' => $assigneeUser->id]], false));
    $response->assertOk();

    expect($response['meta']['total'])
        ->toBe(1);
});

dataset('sorts', [
    // requestKey, firstAttributes, secondAttributes, responseKey, responseFirstValue, responseSecondValue
    '`title`' => ['title', ['title' => 'Alpha'], ['title' => 'Zulu'], 'title', 'Alpha', 'Zulu'],
    '`service_request_number`' => ['service_request_number', ['service_request_number' => 'SR-0001'], ['service_request_number' => 'SR-9999'], 'service_request_number', 'SR-0001', 'SR-9999'],
]);

it('can sort service requests by all attributes ascending', function (string $requestKey, array $firstAttributes, array $secondAttributes, string $responseKey, mixed $responseFirstValue, mixed $responseSecondValue) {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('service_request.view-any');
    Sanctum::actingAs($user, ['api']);

    ServiceRequest::factory()->create($firstAttributes);
    ServiceRequest::factory()->create($secondAttributes);

    $response = getJson(route('api.v1.service-requests.index', ['sort' => $requestKey], false));
    $response->assertOk();

    expect($response['data'][0][$responseKey])
        ->toBe($responseFirstValue);
    expect($response['data'][1][$responseKey])
        ->toBe($responseSecondValue);
})->with('sorts');

it('can sort service requests by all attributes descending', function (string $requestKey, array $firstAttributes, array $secondAttributes, string $responseKey, mixed $responseFirstValue, mixed $responseSecondValue) {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('service_request.view-any');
    Sanctum::actingAs($user, ['api']);

    ServiceRequest::factory()->create($firstAttributes);
    ServiceRequest::factory()->create($secondAttributes);

    $response = getJson(route('api.v1.service-requests.index', ['sort' => '-' . $requestKey], false));
    $response->assertOk();

    expect($response['data'][0][$responseKey])
        ->toBe($responseSecondValue);
    expect($response['data'][1][$responseKey])
        ->toBe($responseFirstValue);
})->with('sorts');

it('can sort service requests by created_at ascending', function () {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('service_request.view-any');
    Sanctum::actingAs($user, ['api']);

    $first = ServiceRequest::factory()->create();
    $first->update(['created_at' => now()->subDay()]);
    $second = ServiceRequest::factory()->create();
    $second->update(['created_at' => now()]);

    $response = getJson(route('api.v1.service-requests.index', ['sort' => 'created_at'], false));
    $response->assertOk();

    expect($response['data'][0]['id'])
        ->toBe($first->id);
    expect($response['data'][1]['id'])
        ->toBe($second->id);
});

it('can sort service requests by created_at descending', function () {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('service_request.view-any');
    Sanctum::actingAs($user, ['api']);

    $first = ServiceRequest::factory()->create(['created_at' => now()->subDay()]);
    $second = ServiceRequest::factory()->create(['created_at' => now()]);

    $response = getJson(route('api.v1.service-requests.index', ['sort' => '-created_at'], false));
    $response->assertOk();

    expect($response['data'][0]['id'])
        ->toBe($second->id);
    expect($response['data'][1]['id'])
        ->toBe($first->id);
});
