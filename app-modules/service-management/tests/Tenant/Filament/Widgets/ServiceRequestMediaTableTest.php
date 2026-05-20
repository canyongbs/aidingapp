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
use AidingApp\ServiceManagement\Filament\Widgets\ServiceRequestMediaTable;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestUpdate;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

describe('ServiceRequest', function () {
    test('renders uploaded files', function () {
        Storage::fake('s3');

        asSuperAdmin();

        $serviceRequest = ServiceRequest::factory()->create();
        $serviceRequest
            ->addMedia(UploadedFile::fake()->image('report.png'))
            ->usingName('report')
            ->toMediaCollection('uploads');

        livewire(ServiceRequestMediaTable::class, [
            'record' => $serviceRequest,
            'collectionName' => 'uploads',
        ])
            ->assertSuccessful()
            ->assertCanSeeTableRecords($serviceRequest->getMedia('uploads'));
    });

    test('shows uploader name for a User', function () {
        Storage::fake('s3');

        $user = User::factory()->create(['name' => 'Jane Doe']);
        actingAs($user);

        $serviceRequest = ServiceRequest::factory()->create();
        $media = $serviceRequest
            ->addMedia(UploadedFile::fake()->image('report.png'))
            ->usingName('report')
            ->toMediaCollection('uploads');

        asSuperAdmin();

        livewire(ServiceRequestMediaTable::class, [
            'record' => $serviceRequest,
            'collectionName' => 'uploads',
        ])
            ->assertSuccessful()
            ->assertTableColumnStateSet('created_by_name', 'Jane Doe', record: $media);
    });

    test('shows uploader name for a Contact', function () {
        Storage::fake('s3');

        asSuperAdmin();

        $contact = Contact::factory()->create([
            'first_name' => 'Carol',
            'last_name' => 'Williams',
            'full_name' => 'Carol Williams',
        ]);

        $serviceRequest = ServiceRequest::factory()->create();
        $media = $serviceRequest
            ->addMedia(UploadedFile::fake()->image('contact-file.png'))
            ->usingName('contact-file')
            ->toMediaCollection('uploads');

        $media->createdBy()->associate($contact);
        $media->save();

        livewire(ServiceRequestMediaTable::class, [
            'record' => $serviceRequest,
            'collectionName' => 'uploads',
        ])
            ->assertSuccessful()
            ->assertTableColumnStateSet('created_by_name', 'Carol Williams', record: $media);
    });

    test('can search by file name (case-insensitive)', function () {
        Storage::fake('s3');

        asSuperAdmin();

        $serviceRequest = ServiceRequest::factory()->create();
        $serviceRequest
            ->addMedia(UploadedFile::fake()->image('Annual-Report.png'))
            ->usingName('Annual-Report')
            ->toMediaCollection('uploads');
        $serviceRequest
            ->addMedia(UploadedFile::fake()->image('invoice.png'))
            ->usingName('invoice')
            ->toMediaCollection('uploads');

        $allMedia = $serviceRequest->getMedia('uploads');
        $annualReport = $allMedia->first(fn ($m) => $m->name === 'Annual-Report');
        $invoice = $allMedia->first(fn ($m) => $m->name === 'invoice');

        livewire(ServiceRequestMediaTable::class, [
            'record' => $serviceRequest,
            'collectionName' => 'uploads',
        ])
            ->searchTable('annual-report')
            ->assertCanSeeTableRecords([$annualReport])
            ->assertCanNotSeeTableRecords([$invoice]);
    });

    test('can search by User uploader name (case-insensitive)', function () {
        Storage::fake('s3');

        $userAlice = User::factory()->create(['name' => 'Alice Smith']);
        $userBob = User::factory()->create(['name' => 'Bob Jones']);

        $serviceRequest = ServiceRequest::factory()->create();

        actingAs($userAlice);
        $serviceRequest
            ->addMedia(UploadedFile::fake()->image('alice-file.png'))
            ->usingName('alice-file')
            ->toMediaCollection('uploads');

        actingAs($userBob);
        $serviceRequest
            ->addMedia(UploadedFile::fake()->image('bob-file.png'))
            ->usingName('bob-file')
            ->toMediaCollection('uploads');

        $allMedia = $serviceRequest->getMedia('uploads');
        $aliceMedia = $allMedia->first(fn ($m) => $m->name === 'alice-file');
        $bobMedia = $allMedia->first(fn ($m) => $m->name === 'bob-file');

        asSuperAdmin();

        livewire(ServiceRequestMediaTable::class, [
            'record' => $serviceRequest,
            'collectionName' => 'uploads',
        ])
            ->searchTable('alice')
            ->assertCanSeeTableRecords([$aliceMedia])
            ->assertCanNotSeeTableRecords([$bobMedia]);
    });

    test('can search by Contact uploader first name (case-insensitive)', function () {
        Storage::fake('s3');

        asSuperAdmin();

        $contactAlice = Contact::factory()->create([
            'first_name' => 'Alice',
            'last_name' => 'Smith',
            'full_name' => 'Alice Smith',
        ]);
        $contactBob = Contact::factory()->create([
            'first_name' => 'Bob',
            'last_name' => 'Jones',
            'full_name' => 'Bob Jones',
        ]);

        $serviceRequest = ServiceRequest::factory()->create();

        $aliceMedia = $serviceRequest
            ->addMedia(UploadedFile::fake()->image('alice-file.png'))
            ->usingName('alice-file')
            ->toMediaCollection('uploads');
        $aliceMedia->createdBy()->associate($contactAlice);
        $aliceMedia->save();

        $bobMedia = $serviceRequest
            ->addMedia(UploadedFile::fake()->image('bob-file.png'))
            ->usingName('bob-file')
            ->toMediaCollection('uploads');
        $bobMedia->createdBy()->associate($contactBob);
        $bobMedia->save();

        livewire(ServiceRequestMediaTable::class, [
            'record' => $serviceRequest,
            'collectionName' => 'uploads',
        ])
            ->searchTable('ALICE')
            ->assertCanSeeTableRecords([$aliceMedia])
            ->assertCanNotSeeTableRecords([$bobMedia]);
    });

    test('can search by Contact uploader last name (case-insensitive)', function () {
        Storage::fake('s3');

        asSuperAdmin();

        $contactAlice = Contact::factory()->create([
            'first_name' => 'Alice',
            'last_name' => 'Smith',
            'full_name' => 'Alice Smith',
        ]);
        $contactBob = Contact::factory()->create([
            'first_name' => 'Bob',
            'last_name' => 'Jones',
            'full_name' => 'Bob Jones',
        ]);

        $serviceRequest = ServiceRequest::factory()->create();

        $aliceMedia = $serviceRequest
            ->addMedia(UploadedFile::fake()->image('alice-file.png'))
            ->usingName('alice-file')
            ->toMediaCollection('uploads');
        $aliceMedia->createdBy()->associate($contactAlice);
        $aliceMedia->save();

        $bobMedia = $serviceRequest
            ->addMedia(UploadedFile::fake()->image('bob-file.png'))
            ->usingName('bob-file')
            ->toMediaCollection('uploads');
        $bobMedia->createdBy()->associate($contactBob);
        $bobMedia->save();

        livewire(ServiceRequestMediaTable::class, [
            'record' => $serviceRequest,
            'collectionName' => 'uploads',
        ])
            ->searchTable('SMITH')
            ->assertCanSeeTableRecords([$aliceMedia])
            ->assertCanNotSeeTableRecords([$bobMedia]);
    });

    test('can search by Contact uploader full name (case-insensitive)', function () {
        Storage::fake('s3');

        asSuperAdmin();

        $contactAlice = Contact::factory()->create([
            'first_name' => 'Alice',
            'last_name' => 'Smith',
            'full_name' => 'Alice Smith',
        ]);
        $contactBob = Contact::factory()->create([
            'first_name' => 'Bob',
            'last_name' => 'Jones',
            'full_name' => 'Bob Jones',
        ]);

        $serviceRequest = ServiceRequest::factory()->create();

        $aliceMedia = $serviceRequest
            ->addMedia(UploadedFile::fake()->image('alice-file.png'))
            ->usingName('alice-file')
            ->toMediaCollection('uploads');
        $aliceMedia->createdBy()->associate($contactAlice);
        $aliceMedia->save();

        $bobMedia = $serviceRequest
            ->addMedia(UploadedFile::fake()->image('bob-file.png'))
            ->usingName('bob-file')
            ->toMediaCollection('uploads');
        $bobMedia->createdBy()->associate($contactBob);
        $bobMedia->save();

        livewire(ServiceRequestMediaTable::class, [
            'record' => $serviceRequest,
            'collectionName' => 'uploads',
        ])
            ->searchTable('alice smith')
            ->assertCanSeeTableRecords([$aliceMedia])
            ->assertCanNotSeeTableRecords([$bobMedia]);
    });

    test('shows empty state when no uploads exist', function () {
        asSuperAdmin();

        $serviceRequest = ServiceRequest::factory()->create();

        livewire(ServiceRequestMediaTable::class, [
            'record' => $serviceRequest,
            'collectionName' => 'uploads',
        ])
            ->assertSuccessful()
            ->assertSeeText('No uploads');
    });
});

describe('ServiceRequestUpdate', function () {
    test('renders uploaded files', function () {
        Storage::fake('s3');

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

    test('shows uploader name for a Contact', function () {
        Storage::fake('s3');

        asSuperAdmin();

        $contact = Contact::factory()->create([
            'first_name' => 'Carol',
            'last_name' => 'Williams',
            'full_name' => 'Carol Williams',
        ]);

        $serviceRequestUpdate = ServiceRequestUpdate::factory()->create();
        $media = $serviceRequestUpdate
            ->addMedia(UploadedFile::fake()->image('contact-file.png'))
            ->usingName('contact-file')
            ->toMediaCollection('uploads');

        $media->createdBy()->associate($contact);
        $media->save();

        livewire(ServiceRequestMediaTable::class, [
            'record' => $serviceRequestUpdate,
            'collectionName' => 'uploads',
        ])
            ->assertSuccessful()
            ->assertTableColumnStateSet('created_by_name', 'Carol Williams', record: $media);
    });

    test('can search by file name (case-insensitive)', function () {
        Storage::fake('s3');

        asSuperAdmin();

        $serviceRequestUpdate = ServiceRequestUpdate::factory()->create();
        $serviceRequestUpdate
            ->addMedia(UploadedFile::fake()->image('Meeting-Notes.png'))
            ->usingName('Meeting-Notes')
            ->toMediaCollection('uploads');
        $serviceRequestUpdate
            ->addMedia(UploadedFile::fake()->image('screenshot.png'))
            ->usingName('screenshot')
            ->toMediaCollection('uploads');

        $allMedia = $serviceRequestUpdate->getMedia('uploads');
        $meetingNotes = $allMedia->first(fn ($m) => $m->name === 'Meeting-Notes');
        $screenshot = $allMedia->first(fn ($m) => $m->name === 'screenshot');

        livewire(ServiceRequestMediaTable::class, [
            'record' => $serviceRequestUpdate,
            'collectionName' => 'uploads',
        ])
            ->searchTable('meeting-notes')
            ->assertCanSeeTableRecords([$meetingNotes])
            ->assertCanNotSeeTableRecords([$screenshot]);
    });

    test('can search by User uploader name', function () {
        Storage::fake('s3');

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

    test('can search by Contact uploader name', function () {
        Storage::fake('s3');

        asSuperAdmin();

        $contactAlice = Contact::factory()->create([
            'first_name' => 'Alice',
            'last_name' => 'Smith',
            'full_name' => 'Alice Smith',
        ]);
        $contactBob = Contact::factory()->create([
            'first_name' => 'Bob',
            'last_name' => 'Jones',
            'full_name' => 'Bob Jones',
        ]);

        $serviceRequestUpdate = ServiceRequestUpdate::factory()->create();

        $aliceMedia = $serviceRequestUpdate
            ->addMedia(UploadedFile::fake()->image('alice-file.png'))
            ->usingName('alice-file')
            ->toMediaCollection('uploads');
        $aliceMedia->createdBy()->associate($contactAlice);
        $aliceMedia->save();

        $bobMedia = $serviceRequestUpdate
            ->addMedia(UploadedFile::fake()->image('bob-file.png'))
            ->usingName('bob-file')
            ->toMediaCollection('uploads');
        $bobMedia->createdBy()->associate($contactBob);
        $bobMedia->save();

        livewire(ServiceRequestMediaTable::class, [
            'record' => $serviceRequestUpdate,
            'collectionName' => 'uploads',
        ])
            ->searchTable('Alice')
            ->assertCanSeeTableRecords([$aliceMedia])
            ->assertCanNotSeeTableRecords([$bobMedia]);
    });

    test('shows empty state when no uploads exist', function () {
        asSuperAdmin();

        $serviceRequestUpdate = ServiceRequestUpdate::factory()->create();

        livewire(ServiceRequestMediaTable::class, [
            'record' => $serviceRequestUpdate,
            'collectionName' => 'uploads',
        ])
            ->assertSuccessful()
            ->assertSeeText('No uploads');
    });
});
