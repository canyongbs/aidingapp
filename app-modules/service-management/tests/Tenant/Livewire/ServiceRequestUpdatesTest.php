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

use AidingApp\Ai\Actions\CompletePrompt;
use AidingApp\Contact\Models\Contact;
use AidingApp\ServiceManagement\Enums\ServiceRequestUpdateType;
use AidingApp\ServiceManagement\Enums\SystemServiceRequestClassification;
use AidingApp\ServiceManagement\Livewire\ServiceRequestUpdates;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestStatus;
use AidingApp\ServiceManagement\Models\ServiceRequestUpdate;
use App\Models\User;
use App\Settings\DisplaySettings;
use Carbon\CarbonImmutable;
use Filament\Actions\Testing\TestAction;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Vite;
use Mockery\MockInterface;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\assertSoftDeleted;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

beforeEach(function () {
    asSuperAdmin();
});

function openServiceRequest(): ServiceRequest
{
    return ServiceRequest::factory()
        ->for(ServiceRequestStatus::factory()->state(['classification' => SystemServiceRequestClassification::Open]), 'status')
        ->create();
}

it('lists updates with their authors, visibility, and attachments', function () {
    Storage::fake('s3');

    $serviceRequest = ServiceRequest::factory()->create();
    $customer = Contact::factory()->create(['full_name' => 'Bethany Emory']);
    $agent = User::factory()->create(['name' => 'Cameron Zimny']);

    $customerUpdate = ServiceRequestUpdate::factory()->for($serviceRequest, 'serviceRequest')->create([
        'update' => 'I am still unable to access the service.',
        'internal' => false,
        'created_by_id' => $customer->getKey(),
        'created_by_type' => $customer->getMorphClass(),
    ]);

    $customerUpdate->addMedia(UploadedFile::fake()->createWithContent('browser-console-log.txt', 'Console output'))->toMediaCollection('uploads');

    ServiceRequestUpdate::factory()->for($serviceRequest, 'serviceRequest')->create([
        'update' => 'Escalating to the application team for review.',
        'internal' => true,
        'created_by_id' => $agent->getKey(),
        'created_by_type' => $agent->getMorphClass(),
    ]);

    ServiceRequestUpdate::factory()->for($serviceRequest, 'serviceRequest')->create([
        'update' => 'Try removing the saved profile.',
        'update_type' => ServiceRequestUpdateType::AiResolutionProposed,
        'internal' => false,
        'created_by_id' => $serviceRequest->getKey(),
        'created_by_type' => $serviceRequest->getMorphClass(),
    ]);

    livewire(ServiceRequestUpdates::class, ['serviceRequest' => $serviceRequest])
        ->assertSuccessful()
        ->assertSeeText('3 updates')
        ->assertSeeTextInOrder(['Bethany Emory', 'Customer', 'I am still unable to access the service.', 'browser-console-log.txt'])
        ->assertSeeTextInOrder(['Cameron Zimny', 'Service Provider', 'Internal · Staff only', 'Escalating to the application team for review.'])
        ->assertSeeTextInOrder(['AI Assistant', 'AI', 'Proposed Resolution', 'Try removing the saved profile.'])
        ->assertSeeHtml('ui-avatars.com/api/?name=B+E')
        ->assertSeeHtml(Vite::asset('resources/images/canyon-ai-headshot.jpg'));
});

it('shows the latest updates first and loads earlier updates on request', function () {
    $serviceRequest = ServiceRequest::factory()->create();
    $contact = Contact::factory()->create();

    foreach (range(1, 51) as $number) {
        ServiceRequestUpdate::factory()->for($serviceRequest, 'serviceRequest')->create([
            'update' => "Update number {$number}.",
            'created_by_id' => $contact->getKey(),
            'created_by_type' => $contact->getMorphClass(),
            'created_at' => now()->subMinutes(60 - $number),
        ]);
    }

    livewire(ServiceRequestUpdates::class, ['serviceRequest' => $serviceRequest])
        ->assertSeeText('51 updates')
        ->assertSeeText('Show earlier updates')
        ->assertSeeText('Update number 51.')
        ->assertDontSeeText('Update number 1.')
        ->call('loadEarlierUpdates')
        ->assertSeeText('Update number 1.')
        ->assertDontSeeText('Show earlier updates');
});

it('polls for updates added after the feed loads', function () {
    $serviceRequest = ServiceRequest::factory()->create();

    $component = livewire(ServiceRequestUpdates::class, ['serviceRequest' => $serviceRequest])
        ->assertSeeHtml('wire:poll.15s')
        ->assertDontSeeText('A reply from the portal.');

    ServiceRequestUpdate::factory()->for($serviceRequest, 'serviceRequest')->create([
        'update' => 'A reply from the portal.',
    ]);

    $component
        ->call('$refresh')
        ->assertSeeText('A reply from the portal.');
});

it('displays update times in the user timezone', function () {
    $user = User::factory()->create(['timezone' => 'America/Denver']);
    asSuperAdmin($user);

    $serviceRequest = ServiceRequest::factory()->create();

    ServiceRequestUpdate::factory()->for($serviceRequest, 'serviceRequest')->create([
        'created_at' => CarbonImmutable::create(2026, 10, 5, 17, 7, 0, 'UTC'),
    ]);

    livewire(ServiceRequestUpdates::class, ['serviceRequest' => $serviceRequest])
        ->assertSeeText('October 5, 2026 · MDT')
        ->assertSeeText('11:07 am')
        ->assertSeeHtml('title="Oct 5, 2026 11:07 am (MDT)"');
});

it('displays update times in the system timezone when the user has none', function () {
    $user = User::factory()->create();
    $user->timezone = null;
    asSuperAdmin($user);

    $settings = app(DisplaySettings::class);
    $settings->timezone = 'Asia/Tokyo';
    $settings->save();

    $serviceRequest = ServiceRequest::factory()->create();

    ServiceRequestUpdate::factory()->for($serviceRequest, 'serviceRequest')->create([
        'created_at' => CarbonImmutable::create(2026, 10, 5, 17, 7, 0, 'UTC'),
    ]);

    livewire(ServiceRequestUpdates::class, ['serviceRequest' => $serviceRequest])
        ->assertSeeText('October 6, 2026 · JST')
        ->assertSeeText('2:07 am');
});

it('can create an update', function (bool $isInternal) {
    $serviceRequest = openServiceRequest();
    $status = ServiceRequestStatus::factory()->create(['classification' => SystemServiceRequestClassification::InProgress]);

    assertDatabaseMissing(ServiceRequestUpdate::class, ['update' => 'We are looking into this.']);

    livewire(ServiceRequestUpdates::class, ['serviceRequest' => $serviceRequest])
        ->fillForm([
            'update' => 'We are looking into this.',
            'internal' => (int) $isInternal,
            'status_id' => $status->getKey(),
        ])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertNotified()
        ->assertSet('data.update', null)
        ->assertSeeText('We are looking into this.');

    assertDatabaseHas(ServiceRequestUpdate::class, [
        'service_request_id' => $serviceRequest->getKey(),
        'update' => 'We are looking into this.',
        'internal' => $isInternal,
        'created_by_id' => auth()->id(),
        'created_by_type' => (new User())->getMorphClass(),
    ]);

    expect($serviceRequest->refresh()->status_id)->toBe($status->getKey());
})->with([
    'visible to customer' => false,
    'internal' => true,
]);

it('attaches uploaded files to the update', function () {
    Storage::fake('s3');

    $serviceRequest = openServiceRequest();

    livewire(ServiceRequestUpdates::class, ['serviceRequest' => $serviceRequest])
        ->fillForm([
            'update' => 'Here is the log.',
            'uploads' => [UploadedFile::fake()->createWithContent('browser-console-log.txt', 'Console output')],
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $update = ServiceRequestUpdate::query()->where('update', 'Here is the log.')->sole();

    expect($update->getMedia('uploads')->pluck('name')->all())->toBe(['browser-console-log']);
});

it('drafts an update with AI', function () {
    $serviceRequest = ServiceRequest::factory()->create();

    $this->mock(CompletePrompt::class, function (MockInterface $mock) {
        $mock->shouldReceive('execute')->once()->andReturn('Drafted update content.');
    });

    livewire(ServiceRequestUpdates::class, ['serviceRequest' => $serviceRequest])
        ->callAction(TestAction::make('draftWithAi')->schemaComponent('update'), data: [
            'instructions' => 'Ask for a screenshot.',
        ])
        ->assertHasNoActionErrors()
        ->assertSet('data.update', 'Drafted update content.');
});

it('validates the inputs', function (array $data, array $errors) {
    $serviceRequest = openServiceRequest();

    livewire(ServiceRequestUpdates::class, ['serviceRequest' => $serviceRequest])
        ->fillForm($data)
        ->call('create')
        ->assertHasFormErrors($errors);
})->with([
    'update required' => [['update' => null], ['update' => 'required']],
]);

it('hides the composer when the service request is closed', function () {
    $serviceRequest = ServiceRequest::factory()
        ->for(ServiceRequestStatus::factory()->state(['classification' => SystemServiceRequestClassification::Closed]), 'status')
        ->create();

    livewire(ServiceRequestUpdates::class, ['serviceRequest' => $serviceRequest])
        ->assertSeeText('This service request is closed')
        ->assertDontSeeText('Send update')
        ->fillForm(['update' => 'Too late.'])
        ->call('create')
        ->assertForbidden();

    assertDatabaseMissing(ServiceRequestUpdate::class, ['update' => 'Too late.']);
});

describe('deletion', function () {
    it('can delete an update', function () {
        $update = ServiceRequestUpdate::factory()->create();

        livewire(ServiceRequestUpdates::class, ['serviceRequest' => $update->serviceRequest])
            ->callAction(TestAction::make('deleteUpdate')->arguments(['update' => $update->getKey()]));

        assertSoftDeleted($update);
    });

    it('does not delete an update from another service request', function () {
        $serviceRequest = ServiceRequest::factory()->create();
        $otherUpdate = ServiceRequestUpdate::factory()->create();

        livewire(ServiceRequestUpdates::class, ['serviceRequest' => $serviceRequest])
            ->assertActionHidden(TestAction::make('deleteUpdate')->arguments(['update' => $otherUpdate->getKey()]));

        expect($otherUpdate->refresh()->trashed())->toBeFalse();
    });
});

describe('authorization', function () {
    it('denies access without the `service_request_update.view-any` permission', function () {
        actingAs(User::factory()->create());

        livewire(ServiceRequestUpdates::class, ['serviceRequest' => ServiceRequest::factory()->create()])
            ->assertForbidden();
    });

    it('does not create an update without the `service_request_update.create` permission', function () {
        $user = User::factory()->create();
        $user->givePermissionTo('service_request_update.view-any');
        actingAs($user);

        $serviceRequest = openServiceRequest();

        livewire(ServiceRequestUpdates::class, ['serviceRequest' => $serviceRequest])
            ->assertDontSeeText('Send update')
            ->fillForm(['update' => 'Unauthorized update.'])
            ->call('create')
            ->assertForbidden();

        assertDatabaseMissing(ServiceRequestUpdate::class, ['update' => 'Unauthorized update.']);
    });

    it('does not change the status without permission to update the service request', function () {
        $user = User::factory()->create();
        $user->givePermissionTo(['service_request_update.view-any', 'service_request_update.create']);
        actingAs($user);

        $serviceRequest = openServiceRequest();
        $originalStatusId = $serviceRequest->status_id;
        $status = ServiceRequestStatus::factory()->create(['classification' => SystemServiceRequestClassification::InProgress]);

        livewire(ServiceRequestUpdates::class, ['serviceRequest' => $serviceRequest])
            ->assertFormFieldHidden('status_id')
            ->fillForm([
                'update' => 'Update from a non-manager.',
                'status_id' => $status->getKey(),
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        assertDatabaseHas(ServiceRequestUpdate::class, ['update' => 'Update from a non-manager.']);

        expect($serviceRequest->refresh()->status_id)->toBe($originalStatusId);
    });

    it('hides the `deleteUpdate` action without the `service_request_update.*.delete` permission', function () {
        $user = User::factory()->create();
        $user->givePermissionTo('service_request_update.view-any');
        actingAs($user);

        $update = ServiceRequestUpdate::factory()->create();

        livewire(ServiceRequestUpdates::class, ['serviceRequest' => $update->serviceRequest])
            ->assertActionHidden(TestAction::make('deleteUpdate')->arguments(['update' => $update->getKey()]));
    });
});
