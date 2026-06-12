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
use AidingApp\ServiceManagement\Models\ServiceRequestUpdate;
use App\Models\SystemUser;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\postJson;

beforeEach(function () {
    config()->set('audit.enabled', false);
});

it('is gated with proper access control', function () {
    $serviceRequest = ServiceRequest::factory()->create();

    $user = SystemUser::factory()->create();
    Sanctum::actingAs($user, ['api']);

    postJson(route('api.v1.service-requests.updates.store', ['serviceRequest' => $serviceRequest], false), [
        'update' => 'Test update',
    ])->assertForbidden();
});

it('requires service_request.view-any permission', function () {
    $serviceRequest = ServiceRequest::factory()->create();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('service_request_update.create');
    Sanctum::actingAs($user, ['api']);

    postJson(route('api.v1.service-requests.updates.store', ['serviceRequest' => $serviceRequest], false), [
        'update' => 'Test update',
    ])->assertForbidden();
});

it('requires service_request_update.create permission', function () {
    $serviceRequest = ServiceRequest::factory()->create();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('service_request.view-any');
    Sanctum::actingAs($user, ['api']);

    postJson(route('api.v1.service-requests.updates.store', ['serviceRequest' => $serviceRequest], false), [
        'update' => 'Test update',
    ])->assertForbidden();
});

it('can create a service request update with valid data', function () {
    $serviceRequest = ServiceRequest::factory()->create();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('service_request.view-any');
    $user->givePermissionTo('service_request_update.create');
    Sanctum::actingAs($user, ['api']);

    $response = postJson(route('api.v1.service-requests.updates.store', ['serviceRequest' => $serviceRequest], false), [
        'update' => 'This is a test update',
        'internal' => true,
    ]);

    $response->assertSuccessful();
    $response->assertJsonFragment([
        'update' => 'This is a test update',
        'internal' => true,
    ]);

    expect(ServiceRequestUpdate::where('service_request_id', $serviceRequest->id)->count())
        ->toBe(1);
    expect(ServiceRequestUpdate::where('update', 'This is a test update')->first()->internal)
        ->toBe(true);
});

it('can create a service request update with only required fields', function () {
    $serviceRequest = ServiceRequest::factory()->create();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('service_request.view-any');
    $user->givePermissionTo('service_request_update.create');
    Sanctum::actingAs($user, ['api']);

    $response = postJson(route('api.v1.service-requests.updates.store', ['serviceRequest' => $serviceRequest], false), [
        'update' => 'Test update',
    ]);

    $response->assertSuccessful();
    $response->assertJsonFragment([
        'update' => 'Test update',
    ]);
});

it('validates that update field is required', function () {
    $serviceRequest = ServiceRequest::factory()->create();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('service_request.view-any');
    $user->givePermissionTo('service_request_update.create');
    Sanctum::actingAs($user, ['api']);

    postJson(route('api.v1.service-requests.updates.store', ['serviceRequest' => $serviceRequest], false), [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['update']);
});

it('validates that update field must be a string', function () {
    $serviceRequest = ServiceRequest::factory()->create();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('service_request.view-any');
    $user->givePermissionTo('service_request_update.create');
    Sanctum::actingAs($user, ['api']);

    postJson(route('api.v1.service-requests.updates.store', ['serviceRequest' => $serviceRequest], false), [
        'update' => 12345,
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['update']);
});

it('validates that internal field must be a boolean', function () {
    $serviceRequest = ServiceRequest::factory()->create();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('service_request.view-any');
    $user->givePermissionTo('service_request_update.create');
    Sanctum::actingAs($user, ['api']);

    postJson(route('api.v1.service-requests.updates.store', ['serviceRequest' => $serviceRequest], false), [
        'update' => 'Test update',
        'internal' => 'not-a-boolean',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['internal']);
});

it('returns 404 for a non-existent service request', function () {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo('service_request.view-any');
    $user->givePermissionTo('service_request_update.create');
    Sanctum::actingAs($user, ['api']);

    postJson(route('api.v1.service-requests.updates.store', ['serviceRequest' => 'non-existent-id'], false), [
        'update' => 'Test update',
    ])->assertNotFound();
});

it('can create a service request update with file attachments', function () {
    Storage::fake('s3');

    $serviceRequest = ServiceRequest::factory()->create();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('service_request.view-any');
    $user->givePermissionTo('service_request_update.create');
    Sanctum::actingAs($user, ['api']);

    $response = postJson(route('api.v1.service-requests.updates.store', ['serviceRequest' => $serviceRequest], false), [
        'update' => 'Update with file',
        'files' => [
            UploadedFile::fake()->createWithContent('document.pdf', 'dummy content for text plain file'),
        ],
    ]);

    $response->assertSuccessful();

    $serviceRequestUpdate = ServiceRequestUpdate::where('service_request_id', $serviceRequest->id)->first();

    expect($serviceRequestUpdate)->not->toBeNull();
    expect($serviceRequestUpdate->getMedia('uploads'))->toHaveCount(1);
});

it('rejects unauthenticated requests', function () {
    $serviceRequest = ServiceRequest::factory()->create();

    postJson(route('api.v1.service-requests.updates.store', ['serviceRequest' => $serviceRequest], false), [
        'update' => 'Test update',
    ])->assertUnauthorized();
});

it('persists the update associated with the correct service request', function () {
    $serviceRequest = ServiceRequest::factory()->create();
    $otherServiceRequest = ServiceRequest::factory()->create();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('service_request.view-any');
    $user->givePermissionTo('service_request_update.create');
    Sanctum::actingAs($user, ['api']);

    postJson(route('api.v1.service-requests.updates.store', ['serviceRequest' => $serviceRequest], false), [
        'update' => 'Correct service request update',
    ])->assertSuccessful();

    expect(ServiceRequestUpdate::where('service_request_id', $serviceRequest->id)->count())
        ->toBe(1);
    expect(ServiceRequestUpdate::where('service_request_id', $otherServiceRequest->id)->count())
        ->toBe(0);
    expect(ServiceRequestUpdate::where('update', 'Correct service request update')->first()->service_request_id)
        ->toBe($serviceRequest->id);
});
