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

use AidingApp\Department\Models\Department;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestUpdates\ServiceRequestUpdateResource;
use AidingApp\ServiceManagement\Filament\Widgets\ServiceRequestMediaTable;
use AidingApp\ServiceManagement\Models\ServiceRequestUpdate;
use App\Models\User;
use App\Settings\LicenseSettings;
use Illuminate\Http\UploadedFile;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

test('The correct details are displayed on the ViewServiceRequestUpdate page', function () {
    $serviceRequestUpdate = ServiceRequestUpdate::factory()->create();

    asSuperAdmin()
        ->get(
            ServiceRequestUpdateResource::getUrl('view', [
                'record' => $serviceRequestUpdate,
                'service_request' => $serviceRequestUpdate->service_request_id,
            ])
        )
        ->assertSuccessful()
        ->assertSeeTextInOrder(
            [
                'Service Request',
                $serviceRequestUpdate->serviceRequest->service_request_number,
                'Internal',
                // TODO: Figure out how to check whether this internal value the check or the X icon
                'Update',
                $serviceRequestUpdate->update,
            ]
        );
});

// Permission Tests

test('ViewServiceRequestUpdate is gated with proper access control', function () {
    $user = User::factory()->create();

    $department = Department::factory()->create();
    $user->department()->associate($department)->save();

    $serviceRequestUpdate = ServiceRequestUpdate::factory()->create();
    $serviceRequestUpdate->serviceRequest->priority->type->managerDepartments()->attach($department);

    actingAs($user)
        ->get(
            ServiceRequestUpdateResource::getUrl('view', [
                'record' => $serviceRequestUpdate,
                'service_request' => $serviceRequestUpdate->service_request_id,
            ])
        )->assertForbidden();

    $user->givePermissionTo('service_request.view-any');
    $user->givePermissionTo('service_request.*.view');
    $user->givePermissionTo('service_request_update.view-any');
    $user->givePermissionTo('service_request_update.*.view');

    actingAs($user)
        ->get(
            ServiceRequestUpdateResource::getUrl('view', [
                'record' => $serviceRequestUpdate,
                'service_request' => $serviceRequestUpdate->service_request_id,
            ])
        )->assertSuccessful();
});

test('ViewServiceRequestUpdate is gated with proper feature access control', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceManagement = false;

    $settings->save();

    $user = User::factory()->create();

    $department = Department::factory()->create();
    $user->department()->associate($department)->save();

    $user->givePermissionTo('service_request.view-any');
    $user->givePermissionTo('service_request.*.view');
    $user->givePermissionTo('service_request_update.view-any');
    $user->givePermissionTo('service_request_update.*.view');

    $serviceRequestUpdate = ServiceRequestUpdate::factory()->create();
    $serviceRequestUpdate->serviceRequest->priority->type->managerDepartments()->attach($department);

    actingAs($user)
        ->get(
            ServiceRequestUpdateResource::getUrl('view', [
                'record' => $serviceRequestUpdate,
                'service_request' => $serviceRequestUpdate->service_request_id,
            ])
        )->assertForbidden();

    $settings->data->addons->serviceManagement = true;

    $settings->save();

    actingAs($user)
        ->get(
            ServiceRequestUpdateResource::getUrl('view', [
                'record' => $serviceRequestUpdate,
                'service_request' => $serviceRequestUpdate->service_request_id,
            ])
        )->assertSuccessful();
});

test('ViewServiceRequestUpdate page displays the uploaded files section', function () {
    $serviceRequestUpdate = ServiceRequestUpdate::factory()->create();
    $serviceRequestUpdate
        ->addMedia(UploadedFile::fake()->image('attachment.png'))
        ->usingName('attachment')
        ->toMediaCollection('uploads');

    asSuperAdmin()
        ->get(
            ServiceRequestUpdateResource::getUrl('view', [
                'record' => $serviceRequestUpdate,
                'service_request' => $serviceRequestUpdate->service_request_id,
            ])
        )
        ->assertSuccessful()
        ->assertSeeText('Uploaded Files')
        ->assertSeeText('File Name')
        ->assertSeeText('Uploaded By')
        ->assertSeeText('Date');
});

test('ServiceRequestMediaTable renders uploaded files on service request update page', function () {
    asSuperAdmin();

    $serviceRequestUpdate = ServiceRequestUpdate::factory()->create();
    $serviceRequestUpdate
        ->addMedia(UploadedFile::fake()->image('attachment.png'))
        ->usingName('attachment')
        ->toMediaCollection('uploads');

    livewire(ServiceRequestMediaTable::class, [
        'record' => $serviceRequestUpdate,
        'collectionName' => 'uploads',
    ])
        ->assertSuccessful()
        ->assertCanSeeTableRecords($serviceRequestUpdate->getMedia('uploads'));
});

test('ServiceRequestMediaTable can search by file name on service request update page', function () {
    asSuperAdmin();

    $serviceRequestUpdate = ServiceRequestUpdate::factory()->create();
    $serviceRequestUpdate
        ->addMedia(UploadedFile::fake()->image('meeting-notes.png'))
        ->usingName('meeting-notes')
        ->toMediaCollection('uploads');
    $serviceRequestUpdate
        ->addMedia(UploadedFile::fake()->image('screenshot.png'))
        ->usingName('screenshot')
        ->toMediaCollection('uploads');

    $allMedia = $serviceRequestUpdate->getMedia('uploads');
    $meetingNotes = $allMedia->first(fn ($m) => $m->name === 'meeting-notes');
    $screenshot = $allMedia->first(fn ($m) => $m->name === 'screenshot');

    livewire(ServiceRequestMediaTable::class, [
        'record' => $serviceRequestUpdate,
        'collectionName' => 'uploads',
    ])
        ->searchTable('meeting')
        ->assertCanSeeTableRecords([$meetingNotes])
        ->assertCanNotSeeTableRecords([$screenshot]);
});

test('ServiceRequestMediaTable can search by uploader name on service request update page', function () {
    $userAlice = User::factory()->create(['name' => 'Alice Smith']);
    $userBob = User::factory()->create(['name' => 'Bob Jones']);

    $serviceRequestUpdate = ServiceRequestUpdate::factory()->create();

    actingAs($userAlice);
    $serviceRequestUpdate
        ->addMedia(UploadedFile::fake()->image('alice-file.png'))
        ->usingName('alice-file')
        ->toMediaCollection('uploads');

    actingAs($userBob);
    $serviceRequestUpdate
        ->addMedia(UploadedFile::fake()->image('bob-file.png'))
        ->usingName('bob-file')
        ->toMediaCollection('uploads');

    $allMedia = $serviceRequestUpdate->getMedia('uploads');
    $aliceMedia = $allMedia->first(fn ($m) => $m->name === 'alice-file');
    $bobMedia = $allMedia->first(fn ($m) => $m->name === 'bob-file');

    asSuperAdmin();

    livewire(ServiceRequestMediaTable::class, [
        'record' => $serviceRequestUpdate,
        'collectionName' => 'uploads',
    ])
        ->searchTable('Alice')
        ->assertCanSeeTableRecords([$aliceMedia])
        ->assertCanNotSeeTableRecords([$bobMedia]);
});

test('ServiceRequestMediaTable shows empty state on service request update page when no uploads exist', function () {
    asSuperAdmin();

    $serviceRequestUpdate = ServiceRequestUpdate::factory()->create();

    livewire(ServiceRequestMediaTable::class, [
        'record' => $serviceRequestUpdate,
        'collectionName' => 'uploads',
    ])
        ->assertSuccessful()
        ->assertSeeText('No uploads');
});
