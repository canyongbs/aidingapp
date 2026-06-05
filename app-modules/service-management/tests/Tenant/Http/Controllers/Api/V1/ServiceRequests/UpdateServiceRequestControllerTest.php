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

use AidingApp\ServiceManagement\Enums\ServiceRequestAssignmentStatus;
use AidingApp\ServiceManagement\Enums\ServiceRequestCategory;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestAssignment;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use AidingApp\ServiceManagement\Tests\Tenant\RequestFactories\UpdateServiceRequestRequestFactory;
use App\Models\SystemUser;
use App\Models\User;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\patchJson;

beforeEach(function () {
    config()->set('audit.enabled', false);
    Queue::fake();
});

it('is gated with proper access control', function () {
    $serviceRequest = ServiceRequest::factory()->create();
    $updateRequestData = UpdateServiceRequestRequestFactory::new()->create();

    $user = SystemUser::factory()->create();
    Sanctum::actingAs($user, ['api']);
    patchJson(route('api.v1.service-requests.update', ['serviceRequest' => $serviceRequest], false), $updateRequestData)
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('service_request.view-any');
    Sanctum::actingAs($user, ['api']);
    patchJson(route('api.v1.service-requests.update', ['serviceRequest' => $serviceRequest], false), $updateRequestData)
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo('service_request.*.update');
    Sanctum::actingAs($user, ['api']);
    patchJson(route('api.v1.service-requests.update', ['serviceRequest' => $serviceRequest], false), $updateRequestData)
        ->assertForbidden();

    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['service_request.view-any', 'service_request.*.update']);
    Sanctum::actingAs($user, ['api']);
    patchJson(route('api.v1.service-requests.update', ['serviceRequest' => $serviceRequest], false), $updateRequestData)
        ->assertOk();
});

it('updates a service request', function () {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['service_request.view-any', 'service_request.*.update']);
    Sanctum::actingAs($user, ['api']);

    $serviceRequest = ServiceRequest::factory()->create();
    $updateRequestData = UpdateServiceRequestRequestFactory::new()->create();

    $response = patchJson(route('api.v1.service-requests.update', ['serviceRequest' => $serviceRequest], false), $updateRequestData);
    $response->assertOk();
    $response->assertJsonStructure([
        'data',
    ]);

    if (isset($updateRequestData['status_id'])) {
        expect($response['data']['status']['id'] ?? null)
            ->toBe($updateRequestData['status_id']);
    }

    if (isset($updateRequestData['priority_id'])) {
        expect($response['data']['priority']['id'] ?? null)
            ->toBe($updateRequestData['priority_id']);
    }

    if (isset($updateRequestData['category'])) {
        $expectedCategory = $updateRequestData['category'] instanceof ServiceRequestCategory
            ? $updateRequestData['category']->value
            : $updateRequestData['category'];

        expect($response['data']['category'] ?? null)
            ->toBe($expectedCategory);
    }

    if (isset($updateRequestData['close_details'])) {
        expect($response['data']['close_details'] ?? null)
            ->toBe($updateRequestData['close_details']);
    }
});

it('updates a service request with an assignment', function () {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['service_request.view-any', 'service_request.*.update']);
    Sanctum::actingAs($user, ['api']);

    $serviceRequest = ServiceRequest::factory()->create();
    $assigneeUser = User::factory()->create();
    $serviceRequest->priority->type->managerUsers()->attach($assigneeUser);

    $response = patchJson(route('api.v1.service-requests.update', ['serviceRequest' => $serviceRequest], false), [
        'assigned_to_id' => $assigneeUser->id,
    ]);
    $response->assertOk();

    expect($response['data']['assignee'])->toBe([
        'id' => $assigneeUser->id,
        'name' => $assigneeUser->name,
    ]);

    $assignment = ServiceRequestAssignment::where('service_request_id', $serviceRequest->id)
        ->where('user_id', $assigneeUser->id)
        ->where('status', ServiceRequestAssignmentStatus::Active)
        ->first();

    expect($assignment)->not->toBeNull();
    expect($assignment->assigned_by_id)->toBe($user->id);
    expect($assignment->assigned_by_type)->toBe($user->getMorphClass());
});

it('denies update on a closed service request', function () {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['service_request.view-any', 'service_request.*.update']);
    Sanctum::actingAs($user, ['api']);

    $closedStatus = ServiceRequestStatus::factory()->closed()->create();
    $serviceRequest = ServiceRequest::factory()->create([
        'status_id' => $closedStatus->id,
    ]);

    $response = patchJson(route('api.v1.service-requests.update', ['serviceRequest' => $serviceRequest], false), [
        'close_details' => 'Updated close details',
    ]);
    $response->assertForbidden();
});

it('validates', function (array $requestAttributes, string $invalidAttribute, string $validationMessage) {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['service_request.view-any', 'service_request.*.update']);
    Sanctum::actingAs($user, ['api']);

    $serviceRequest = ServiceRequest::factory()->create();
    $updateRequestData = UpdateServiceRequestRequestFactory::new()->create($requestAttributes);

    $response = patchJson(route('api.v1.service-requests.update', ['serviceRequest' => $serviceRequest], false), $updateRequestData);
    $response->assertUnprocessable();
    $response->assertJsonValidationErrors([
        $invalidAttribute => [$validationMessage],
    ]);
})->with([
    '`status_id` must be a valid UUID' => [['status_id' => 'not-a-uuid'], 'status_id', 'The status id must be a valid UUID.'],
    '`status_id` must exist' => [['status_id' => (string) Str::orderedUuid()], 'status_id', 'The selected status id is invalid.'],
    '`priority_id` must be a valid UUID' => [['priority_id' => 'not-a-uuid'], 'priority_id', 'The priority id must be a valid UUID.'],
    '`priority_id` must exist' => [['priority_id' => (string) Str::orderedUuid()], 'priority_id', 'The selected priority id is invalid.'],
    '`assigned_to_id` must be a valid UUID' => [['assigned_to_id' => 'not-a-uuid'], 'assigned_to_id', 'The assigned to id must be a valid UUID.'],
    '`assigned_to_id` must exist' => [['assigned_to_id' => (string) Str::orderedUuid()], 'assigned_to_id', 'The selected assigned to id is invalid.'],
    '`category` must be valid' => [['category' => 'invalid-category'], 'category', 'The selected category is invalid.'],
    '`category` max 255 characters' => [['category' => str_repeat('a', 256)], 'category', 'The category may not be greater than 255 characters.'],
]);

it('returns correct service request fields after update', function (string $responseKey, Closure $getExpected) {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['service_request.view-any', 'service_request.*.update']);
    Sanctum::actingAs($user, ['api']);

    $serviceRequest = ServiceRequest::factory()->create();
    $updateRequestData = UpdateServiceRequestRequestFactory::new()->create();

    $response = patchJson(route('api.v1.service-requests.update', ['serviceRequest' => $serviceRequest], false), $updateRequestData);
    $response->assertOk();

    expect($response['data'][$responseKey])->toBe($getExpected($serviceRequest->fresh()));
})->with([
    '`id`' => ['id', fn (ServiceRequest $sr) => $sr->id],
    '`service_request_number`' => ['service_request_number', fn (ServiceRequest $sr) => $sr->service_request_number],
    '`title`' => ['title', fn (ServiceRequest $sr) => $sr->title],
    '`category`' => ['category', fn (ServiceRequest $sr) => $sr->category->value],
]);

it('updates close_details', function () {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['service_request.view-any', 'service_request.*.update']);
    Sanctum::actingAs($user, ['api']);

    $serviceRequest = ServiceRequest::factory()->create();
    $newCloseDetails = fake()->sentence();

    $response = patchJson(route('api.v1.service-requests.update', ['serviceRequest' => $serviceRequest], false), [
        'close_details' => $newCloseDetails,
    ]);
    $response->assertOk();

    expect($response['data']['close_details'])->toBe($newCloseDetails);
});

it('returns correct status relationship structure after update', function () {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['service_request.view-any', 'service_request.*.update']);
    Sanctum::actingAs($user, ['api']);

    $newStatus = ServiceRequestStatus::factory()->open()->create();
    $serviceRequest = ServiceRequest::factory()->create();

    $response = patchJson(route('api.v1.service-requests.update', ['serviceRequest' => $serviceRequest], false), [
        'status_id' => $newStatus->id,
    ]);
    $response->assertOk();

    expect($response['data']['status'])->toBe([
        'id' => $newStatus->id,
        'name' => $newStatus->name,
    ]);
});

it('returns correct priority relationship structure after update', function () {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['service_request.view-any', 'service_request.*.update']);
    Sanctum::actingAs($user, ['api']);

    $newPriority = ServiceRequestPriority::factory()->create();
    $serviceRequest = ServiceRequest::factory()->create();

    $response = patchJson(route('api.v1.service-requests.update', ['serviceRequest' => $serviceRequest], false), [
        'priority_id' => $newPriority->id,
    ]);
    $response->assertOk();

    expect($response['data']['priority'])->toBe([
        'id' => $newPriority->id,
        'name' => $newPriority->name,
    ]);
});

it('returns null assignee when no assignment exists', function () {
    $user = SystemUser::factory()->create();
    $user->givePermissionTo(['service_request.view-any', 'service_request.*.update']);
    Sanctum::actingAs($user, ['api']);

    $serviceRequest = ServiceRequest::factory()->create();

    $response = patchJson(route('api.v1.service-requests.update', ['serviceRequest' => $serviceRequest], false), [
        'close_details' => 'Updated details',
    ]);
    $response->assertOk();

    expect($response['data']['assignee'])->toBeNull();
});
