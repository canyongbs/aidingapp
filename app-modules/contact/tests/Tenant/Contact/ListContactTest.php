<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

    Aiding App™ is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Aiding App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/
use AidingApp\Contact\Filament\Resources\ContactResource;
use AidingApp\Contact\Filament\Resources\ContactResource\Pages\EditContact;
use AidingApp\Contact\Filament\Resources\ContactResource\Pages\ListContacts;
use AidingApp\Contact\Filament\Resources\ContactResource\RelationManagers\EngagementsRelationManager;
use AidingApp\Contact\Models\Contact;
use AidingApp\Contact\Models\ContactType;
use AidingApp\Engagement\Models\Engagement;
use AidingApp\Engagement\Models\EngagementResponse;
use AidingApp\Timeline\Events\TimelineableRecordCreated;
use AidingApp\Timeline\Listeners\AddRecordToTimeline;
use App\Models\User;
use Illuminate\Support\Facades\Queue;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

// TODO: Write ListContacts page test
//test('The correct details are displayed on the ListContacts page', function () {});

// TODO: Sorting and Searching tests

// Permission Tests

test('ListContacts is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            ContactResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('contact.view-any');

    actingAs($user)
        ->get(
            ContactResource::getUrl('index')
        )->assertSuccessful();
});

test('ListContacts can bulk update characteristics', function () {
    $user = User::factory()->create();

    $user->givePermissionTo('contact.view-any');

    actingAs($user);

    $contacts = Contact::factory()->count(3)->create();

    $component = livewire(ListContacts::class);

    $component->assertCanSeeTableRecords($contacts)
        ->assertCountTableRecords($contacts->count())
        ->assertTableBulkActionExists('bulk_update');

    $type = ContactType::factory()->create();

    $description = 'abc123';

    $component
        ->callTableBulkAction('bulk_update', $contacts, [
            'field' => 'description',
            'description' => $description,
        ])
        ->assertHasNoTableBulkActionErrors()
        ->callTableBulkAction('bulk_update', $contacts, [
            'field' => 'email_bounce',
            'email_bounce' => true,
        ])
        ->assertHasNoTableBulkActionErrors()
        ->callTableBulkAction('bulk_update', $contacts, [
            'field' => 'sms_opt_out',
            'sms_opt_out' => true,
        ])
        ->assertHasNoTableBulkActionErrors()
        ->callTableBulkAction('bulk_update', $contacts, [
            'field' => 'type_id',
            'type_id' => $type->id,
        ])
        ->assertHasNoTableBulkActionErrors();

    expect($contacts)
        ->each(
            fn ($contact) => $contact
                ->refresh()
                ->description->toBe($description)
                ->email_bounce->toBeTrue()
                ->sms_opt_out->toBeTrue()
                ->type_id->toBe($type->id)
        );
});

test('can list records of engagements timelineable', function () {
    Queue::fake();
    $user = User::factory()->create();

    $contact = Contact::factory()->create();

    actingAs($user)
        ->get(
            ContactResource::getUrl('manage-engagement', [
                'record' => $contact->getKey(),
            ])
        )->assertForbidden();

    $user->givePermissionTo('engagement.view-any');
    $user->givePermissionTo('engagement_response.view-any');

    actingAs($user);

    $engagements = Engagement::factory()->count(1)
        ->state([
            'recipient_id' => $contact->getKey(),
            'recipient_type' => $contact->getMorphClass(),
        ])->createQuietly();

    $engagementResponses = EngagementResponse::factory()->count(5)
        ->state([
            'sender_id' => $contact->getKey(),
            'sender_type' => $contact->getMorphClass(),
        ])->createQuietly();

    $engagements->each(function ($response) {
        $event = new TimelineableRecordCreated($response->recipient, $response);
        $listener = app(AddRecordToTimeline::class);

        $listener->handle($event);
    });

    $engagementResponses->each(function ($response) {
        $event = new TimelineableRecordCreated($response->sender, $response);
        $listener = app(AddRecordToTimeline::class);

        $listener->handle($event);
    });

    livewire(EngagementsRelationManager::class, [
        'ownerRecord' => $contact,
        'pageClass' => EditContact::class,
    ])
        ->assertCanSeeTableRecords($engagements->pluck('timelineRecord')->merge($engagementResponses->pluck('timelineRecord')))
        ->assertSuccessful();
});

test('can filter engagements timelineable', function () {
    Queue::fake();
    $user = User::factory()->create();

    $contact = Contact::factory()->create();

    actingAs($user)
        ->get(
            ContactResource::getUrl('manage-engagement', [
                'record' => $contact->getKey(),
            ])
        )->assertForbidden();

    $user->givePermissionTo('engagement.view-any');
    $user->givePermissionTo('engagement_response.view-any');

    actingAs($user);

    $engagements = Engagement::factory()->count(1)
        ->state([
            'recipient_id' => $contact->getKey(),
            'recipient_type' => $contact->getMorphClass(),
        ])->createQuietly();

    $engagementResponses = EngagementResponse::factory()->count(5)
        ->state([
            'sender_id' => $contact->getKey(),
            'sender_type' => $contact->getMorphClass(),
        ])->createQuietly();

    $engagements->each(function ($response) {
        $event = new TimelineableRecordCreated($response->recipient, $response);
        $listener = app(AddRecordToTimeline::class);

        $listener->handle($event);
    });

    $engagementResponses->each(function ($response) {
        $event = new TimelineableRecordCreated($response->sender, $response);
        $listener = app(AddRecordToTimeline::class);

        $listener->handle($event);
    });

    livewire(EngagementsRelationManager::class, [
        'ownerRecord' => $contact,
        'pageClass' => EditContact::class,
    ])
        ->assertCanSeeTableRecords($engagements->pluck('timelineRecord')->merge($engagementResponses->pluck('timelineRecord')))
        ->filterTable('direction', Engagement::class)
        ->assertCanSeeTableRecords($engagements->pluck('timelineRecord'))
        ->assertCanNotSeeTableRecords($engagementResponses->pluck('timelineRecord'))
        ->removeTableFilter('direction')
        ->filterTable('direction', EngagementResponse::class)
        ->assertCanSeeTableRecords($engagementResponses->pluck('timelineRecord'))
        ->assertCanNotSeeTableRecords($engagements->pluck('timelineRecord'))
        ->assertSuccessful();
});
