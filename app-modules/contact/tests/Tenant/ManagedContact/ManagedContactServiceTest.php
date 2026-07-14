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
use AidingApp\Contact\Models\ContactType;
use AidingApp\Contact\Services\ManagedContactService;
use App\Features\ManagedContactFeature;
use App\Models\User;

it('creates a managed contact synchronized from the user', function () {
    $type = ContactType::factory()->create();

    $user = User::factory()->create([
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'job_title' => 'Engineer',
        'work_number' => '+1 555 111 2222',
        'mobile' => '+1 555 333 4444',
    ]);

    $contact = app(ManagedContactService::class)->enable($user, $type->getKey());

    expect($contact->user_id)->toBe($user->getKey())
        ->and($contact->first_name)->toBe('Jane')
        ->and($contact->last_name)->toBe('Doe')
        ->and($contact->full_name)->toBe('Jane Doe')
        ->and($contact->email)->toBe('jane@example.com')
        ->and($contact->job_title)->toBe('Engineer')
        ->and($contact->phone)->toBe('+1 555 111 2222')
        ->and($contact->mobile)->toBe('+1 555 333 4444')
        ->and($contact->type_id)->toBe($type->getKey())
        ->and($contact->isManaged())->toBeTrue();
});

it('handles a single word name by leaving the last name empty', function () {
    $type = ContactType::factory()->create();

    $user = User::factory()->create(['name' => 'Cher']);

    $contact = app(ManagedContactService::class)->enable($user, $type->getKey());

    expect($contact->first_name)->toBe('Cher')
        ->and($contact->last_name)->toBe('')
        ->and($contact->full_name)->toBe('Cher');
});

it('synchronizes the managed contact when the user is updated', function () {
    $type = ContactType::factory()->create();

    $user = User::factory()->create(['name' => 'Old Name', 'job_title' => 'Junior']);

    app(ManagedContactService::class)->enable($user, $type->getKey());

    $user->update([
        'name' => 'New Name',
        'job_title' => 'Senior',
        'email' => 'new-email@example.com',
        'work_number' => '+1 555 999 8888',
        'mobile' => '+1 555 777 6666',
    ]);

    $contact = $user->managedContact()->first();

    expect($contact->full_name)->toBe('New Name')
        ->and($contact->first_name)->toBe('New')
        ->and($contact->last_name)->toBe('Name')
        ->and($contact->job_title)->toBe('Senior')
        ->and($contact->email)->toBe('new-email@example.com')
        ->and($contact->phone)->toBe('+1 555 999 8888')
        ->and($contact->mobile)->toBe('+1 555 777 6666');
});

it('links and overrides an existing contact with the same email instead of duplicating', function () {
    $type = ContactType::factory()->create();

    $existing = Contact::factory()->create([
        'email' => 'match@example.com',
        'first_name' => 'Old',
    ]);

    $originalTypeId = $existing->type_id;

    $user = User::factory()->create([
        'name' => 'New Person',
        'email' => 'match@example.com',
    ]);

    $contact = app(ManagedContactService::class)->enable($user, $type->getKey());

    expect($contact->getKey())->toBe($existing->getKey())
        ->and($contact->user_id)->toBe($user->getKey())
        ->and($contact->first_name)->toBe('New')
        ->and($contact->type_id)->toBe($type->getKey())
        ->and($contact->type_id)->not->toBe($originalTypeId);

    expect(Contact::query()->where('email', 'match@example.com')->count())->toBe(1);
});

it('restores a soft-deleted contact when linking by email', function () {
    $type = ContactType::factory()->create();

    $existing = Contact::factory()->create(['email' => 'gone@example.com']);
    $existing->delete();

    $user = User::factory()->create(['email' => 'gone@example.com']);

    $contact = app(ManagedContactService::class)->enable($user, $type->getKey());

    expect($contact->getKey())->toBe($existing->getKey())
        ->and($contact->trashed())->toBeFalse()
        ->and($contact->user_id)->toBe($user->getKey());
});

it('does not steal a contact already managed by a different user', function () {
    $type = ContactType::factory()->create();

    $otherUser = User::factory()->create();
    $otherContact = app(ManagedContactService::class)->enable($otherUser, $type->getKey());
    $otherContact->update(['email' => 'shared@example.com']);

    $user = User::factory()->create(['email' => 'shared@example.com']);
    $contact = app(ManagedContactService::class)->enable($user, $type->getKey());

    expect($contact->getKey())->not->toBe($otherContact->getKey())
        ->and($otherContact->fresh()->user_id)->toBe($otherUser->getKey());
});

it('unlinks the managed contact when disabled but keeps it editable', function () {
    $type = ContactType::factory()->create();

    $user = User::factory()->create();

    $contact = app(ManagedContactService::class)->enable($user, $type->getKey());

    app(ManagedContactService::class)->disable($user);

    expect($contact->fresh()->user_id)->toBeNull()
        ->and($contact->fresh()->trashed())->toBeFalse();
});

it('unlinks the managed contact when the user is deleted', function () {
    $type = ContactType::factory()->create();

    $user = User::factory()->create();

    $contact = app(ManagedContactService::class)->enable($user, $type->getKey());

    $user->delete();

    expect($contact->fresh()->user_id)->toBeNull()
        ->and($contact->fresh()->trashed())->toBeFalse();
});

it('does not synchronize when the feature is inactive', function () {
    $type = ContactType::factory()->create();

    $user = User::factory()->create(['name' => 'Original Name']);

    $contact = app(ManagedContactService::class)->enable($user, $type->getKey());

    ManagedContactFeature::deactivate();

    $user->update(['name' => 'Changed Name']);

    expect($contact->fresh()->full_name)->toBe('Original Name');
});
