<?php

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
