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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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

use AidingApp\Audit\Models\Audit;
use AidingApp\ServiceManagement\Enums\SystemServiceRequestClassification;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestPriority;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use App\Models\SystemUser;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\patchJson;

beforeEach(function () {
    config()->set('audit.queue.enable', false);
});

it('attributes audit change_agent to the authenticated SystemUser for API-initiated changes', function () {
    $systemUser = SystemUser::factory()->create(['name' => 'Test System User']);
    $systemUser->givePermissionTo(['service_request.view-any', 'service_request.*.update']);
    Sanctum::actingAs($systemUser, ['api']);

    $serviceRequest = ServiceRequest::factory()->create([
        'status_id' => ServiceRequestStatus::factory()->state(['classification' => SystemServiceRequestClassification::Open]),
    ]);

    $newPriority = ServiceRequestPriority::factory()->create();

    patchJson(
        route('api.v1.service-requests.update', ['serviceRequest' => $serviceRequest], false),
        ['priority_id' => $newPriority->id]
    )->assertOk();

    $audit = Audit::query()
        ->where('auditable_type', $serviceRequest->getMorphClass())
        ->where('auditable_id', $serviceRequest->id)
        ->where('event', 'updated')
        ->first();

    expect($audit)->not->toBeNull();
    expect($audit->change_agent_type)->toBe('system_user');
    expect($audit->change_agent_id)->toBe($systemUser->id);
});

it('resolves the SystemUser name via the audit user() relationship', function () {
    $systemUser = SystemUser::factory()->create(['name' => 'Test System User']);
    $systemUser->givePermissionTo(['service_request.view-any', 'service_request.*.update']);
    Sanctum::actingAs($systemUser, ['api']);

    $serviceRequest = ServiceRequest::factory()->create([
        'status_id' => ServiceRequestStatus::factory()->state(['classification' => SystemServiceRequestClassification::Open]),
    ]);

    $newPriority = ServiceRequestPriority::factory()->create();

    patchJson(
        route('api.v1.service-requests.update', ['serviceRequest' => $serviceRequest], false),
        ['priority_id' => $newPriority->id]
    )->assertOk();

    $audit = Audit::query()
        ->where('auditable_type', $serviceRequest->getMorphClass())
        ->where('auditable_id', $serviceRequest->id)
        ->where('event', 'updated')
        ->first();

    expect($audit->user)->toBeInstanceOf(SystemUser::class);
    expect($audit->user->name)->toBe('Test System User');
});

it('records null change_agent when no user is authenticated', function () {
    $serviceRequest = ServiceRequest::factory()->create([
        'status_id' => ServiceRequestStatus::factory()->state(['classification' => SystemServiceRequestClassification::Open]),
    ]);

    $newPriority = ServiceRequestPriority::factory()->create();
    $serviceRequest->update(['priority_id' => $newPriority->id]);

    $audit = Audit::query()
        ->where('auditable_type', $serviceRequest->getMorphClass())
        ->where('auditable_id', $serviceRequest->id)
        ->where('event', 'updated')
        ->first();

    expect($audit)->not->toBeNull();
    expect($audit->change_agent_type)->toBeNull();
    expect($audit->change_agent_id)->toBeNull();
});
