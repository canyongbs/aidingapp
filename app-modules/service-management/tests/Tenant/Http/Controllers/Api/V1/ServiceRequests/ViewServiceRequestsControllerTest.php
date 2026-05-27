<?php

use AidingApp\ServiceManagement\Models\ServiceRequest;
use App\Models\SystemUser;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\getJson;

beforeEach(function () {
    // Disable auditing, which causes testing issues when authenticating with a fake Sanctum token.
    config()->set('audit.enabled', false);
});

it('is gated with proper access control', function () {
    $serviceRequest = ServiceRequest::factory()->create();

    $user = SystemUser::factory()->create();
    Sanctum::actingAs($user, ['api']);
    getJson(route('api.v1.service-requests.show', ['serviceRequest' => $serviceRequest], false))
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('service_request.view-any');
    Sanctum::actingAs($user, ['api']);
    getJson(route('api.v1.service-requests.show', ['serviceRequest' => $serviceRequest], false))
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('service_request.*.view');
    Sanctum::actingAs($user, ['api']);
    getJson(route('api.v1.service-requests.show', ['serviceRequest' => $serviceRequest], false))
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['service_request.view-any', 'service_request.*.view']);
    Sanctum::actingAs($user, ['api']);
    getJson(route('api.v1.service-requests.show', ['serviceRequest' => $serviceRequest], false))
        ->assertOk();
});

it('returns a service request resource', function () {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['service_request.view-any', 'service_request.*.view']);

    $serviceRequest = ServiceRequest::factory()->create();
    Sanctum::actingAs($user, ['api']);

    $response = getJson(route('api.v1.service-requests.show', ['serviceRequest' => $serviceRequest], false));
    $response->assertOk();
    $response->assertJsonStructure([
        'data',
    ]);

    expect($response['data']['id'])
        ->toBe($serviceRequest->id);
    expect($response['data']['title'])
        ->toBe($serviceRequest->title);
    expect($response['data']['service_request_number'])
        ->toBe($serviceRequest->service_request_number);
});
