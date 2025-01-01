<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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
use AidingApp\Contact\Models\Contact;
use AidingApp\Contact\Models\ContactSource;
use AidingApp\Contact\Models\ContactStatus;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

// TODO: Write ListContacts page test
//test('The correct details are displayed on the ListContacts page', function () {});

// TODO: Sorting and Searching tests

// Permission Tests

test('ListContacts is gated with proper access control', function () {
    $user = User::factory()->licensed(Contact::getLicenseType())->create();

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
    $user = User::factory()->licensed(Contact::getLicenseType())->create();

    $user->givePermissionTo('contact.view-any');

    actingAs($user);

    $contacts = Contact::factory()->count(3)->create();

    $component = livewire(ContactResource\Pages\ListContacts::class);

    $component->assertCanSeeTableRecords($contacts)
        ->assertCountTableRecords($contacts->count())
        ->assertTableBulkActionExists('bulk_update');

    $source = ContactSource::factory()->create();

    $status = ContactStatus::factory()->create();

    $description = 'abc123';

    $component
        ->callTableBulkAction('bulk_update', $contacts, [
            'field' => 'assigned_to_id',
            'assigned_to_id' => $user->id,
        ])
        ->assertHasNoTableBulkActionErrors()
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
            'field' => 'source_id',
            'source_id' => $source->id,
        ])
        ->assertHasNoTableBulkActionErrors()
        ->callTableBulkAction('bulk_update', $contacts, [
            'field' => 'status_id',
            'status_id' => $status->id,
        ])
        ->assertHasNoTableBulkActionErrors();

    expect($contacts)
        ->each(
            fn ($contact) => $contact
                ->refresh()
                ->assigned_to_id->toBe($user->id)
                ->description->toBe($description)
                ->email_bounce->toBeTrue()
                ->sms_opt_out->toBeTrue()
                ->source_id->toBe($source->id)
                ->status_id->toBe($status->id)
        );
});
