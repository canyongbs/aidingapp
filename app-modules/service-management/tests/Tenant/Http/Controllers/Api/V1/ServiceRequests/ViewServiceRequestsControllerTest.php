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
});

it('returns correct service request fields', function (string $responseKey, Closure $getExpected) {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['service_request.view-any', 'service_request.*.view']);

    $serviceRequest = ServiceRequest::factory()->create();
    Sanctum::actingAs($user, ['api']);

    $response = getJson(route('api.v1.service-requests.show', ['serviceRequest' => $serviceRequest], false));
    $response->assertOk();

    expect($response['data'][$responseKey])->toBe($getExpected($serviceRequest));
})->with([
    // responseKey, getExpected
    '`id`' => ['id', fn (ServiceRequest $sr) => $sr->id],
    '`service_request_number`' => ['service_request_number', fn (ServiceRequest $sr) => $sr->service_request_number],
    '`title`' => ['title', fn (ServiceRequest $sr) => $sr->title],
    '`close_details`' => ['close_details', fn (ServiceRequest $sr) => $sr->close_details],
    '`category`' => ['category', fn (ServiceRequest $sr) => $sr->category->value],
]);
