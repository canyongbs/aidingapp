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

use AidingApp\ServiceManagement\Enums\ServiceRequestFileUploadType;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestUpdate;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('resolves the upload type of a file attached to a service request update', function () {
    Storage::fake('s3');

    $media = ServiceRequestUpdate::factory()->create()
        ->addMedia(UploadedFile::fake()->image('report.png'))
        ->toMediaCollection('uploads');

    expect(ServiceRequestFileUploadType::fromMedia($media))->toBe(ServiceRequestFileUploadType::Update);
});

it('resolves the upload type of a file attached directly to a service request', function (?string $uploadType, ?ServiceRequestFileUploadType $expectedUploadType) {
    Storage::fake('s3');

    $media = ServiceRequest::factory()->create()
        ->addMedia(UploadedFile::fake()->image('report.png'))
        ->withCustomProperties(filled($uploadType) ? [ServiceRequestFileUploadType::CUSTOM_PROPERTY => $uploadType] : [])
        ->toMediaCollection('uploads');

    expect(ServiceRequestFileUploadType::fromMedia($media))->toBe($expectedUploadType);
})->with([
    'staff upload' => ['staff_upload', ServiceRequestFileUploadType::StaffUpload],
    'request submission' => ['request_submission', ServiceRequestFileUploadType::RequestSubmission],
    'no upload type recorded' => [null, null],
    'unknown upload type' => ['unknown', null],
]);
