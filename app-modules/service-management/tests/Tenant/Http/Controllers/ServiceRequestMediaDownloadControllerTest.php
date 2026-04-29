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
use AidingApp\ServiceManagement\Models\ServiceRequestUpdate;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Tests\asSuperAdmin;

beforeEach(function () {
    Storage::fake('s3');
});

test('an authenticated user with permission can download service request update media', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('service_request_update.view-any');
    $user->givePermissionTo('service_request_update.*.view');

    $serviceRequestUpdate = ServiceRequestUpdate::factory()->create();
    $serviceRequestUpdate
        ->addMedia(UploadedFile::fake()->image('test-file.png'))
        ->toMediaCollection('uploads');

    $media = $serviceRequestUpdate->getFirstMedia('uploads');

    actingAs($user)
        ->get(route('service-request.media.download', ['media' => $media->getKey()]))
        ->assertRedirect();
});

test('an authenticated user without permission cannot download service request update media', function () {
    $user = User::factory()->create();

    $serviceRequestUpdate = ServiceRequestUpdate::factory()->create();
    $serviceRequestUpdate
        ->addMedia(UploadedFile::fake()->image('test-file.png'))
        ->toMediaCollection('uploads');

    $media = $serviceRequestUpdate->getFirstMedia('uploads');

    actingAs($user)
        ->get(route('service-request.media.download', ['media' => $media->getKey()]))
        ->assertForbidden();
});

test('an unauthenticated user cannot download service request media', function () {
    $serviceRequestUpdate = ServiceRequestUpdate::factory()->create();
    $serviceRequestUpdate
        ->addMedia(UploadedFile::fake()->image('test-file.png'))
        ->toMediaCollection('uploads');

    $media = $serviceRequestUpdate->getFirstMedia('uploads');

    get(route('service-request.media.download', ['media' => $media->getKey()]))
        ->assertRedirect();
});

test('a super admin can download service request update media', function () {
    $serviceRequestUpdate = ServiceRequestUpdate::factory()->create();
    $serviceRequestUpdate
        ->addMedia(UploadedFile::fake()->image('document.png'))
        ->toMediaCollection('uploads');

    $media = $serviceRequestUpdate->getFirstMedia('uploads');

    asSuperAdmin()
        ->get(route('service-request.media.download', ['media' => $media->getKey()]))
        ->assertRedirect();
});

test('an authenticated user with permission can download service request media', function () {
    $serviceRequest = ServiceRequest::factory()->create();
    $serviceRequest
        ->addMedia(UploadedFile::fake()->image('test-file.png'))
        ->toMediaCollection('uploads');

    $media = $serviceRequest->getFirstMedia('uploads');

    asSuperAdmin()
        ->get(route('service-request.media.download', ['media' => $media->getKey()]))
        ->assertRedirect();
});

test('an authenticated user without permission cannot download service request media', function () {
    $user = User::factory()->create();

    $serviceRequest = ServiceRequest::factory()->create();
    $serviceRequest
        ->addMedia(UploadedFile::fake()->image('test-file.png'))
        ->toMediaCollection('uploads');

    $media = $serviceRequest->getFirstMedia('uploads');

    actingAs($user)
        ->get(route('service-request.media.download', ['media' => $media->getKey()]))
        ->assertForbidden();
});

test('the download redirect url contains a temporary url', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('service_request_update.view-any');
    $user->givePermissionTo('service_request_update.*.view');

    $serviceRequestUpdate = ServiceRequestUpdate::factory()->create();
    $serviceRequestUpdate
        ->addMedia(UploadedFile::fake()->image('test-file.png'))
        ->toMediaCollection('uploads');

    $media = $serviceRequestUpdate->getFirstMedia('uploads');

    $response = actingAs($user)
        ->get(route('service-request.media.download', ['media' => $media->getKey()]));

    $response->assertRedirect();
    $redirectUrl = $response->headers->get('Location');
    expect($redirectUrl)->toContain($media->file_name);
});

test('a user cannot download media that does not belong to a service request or service request update', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('service_request_update.view-any');
    $user->givePermissionTo('service_request_update.*.view');
    $user->givePermissionTo('service_request.view-any');
    $user->givePermissionTo('service_request.*.view');

    // Create media on a different model type by using a ServiceRequestUpdate,
    // then manually changing the model_type to simulate an unrecognized model
    $serviceRequestUpdate = ServiceRequestUpdate::factory()->create();
    $serviceRequestUpdate
        ->addMedia(UploadedFile::fake()->image('test-file.png'))
        ->toMediaCollection('uploads');

    $media = $serviceRequestUpdate->getFirstMedia('uploads');
    $media->update(['model_type' => 'unknown_model_type']);

    actingAs($user)
        ->get(route('service-request.media.download', ['media' => $media->getKey()]))
        ->assertForbidden();
});
