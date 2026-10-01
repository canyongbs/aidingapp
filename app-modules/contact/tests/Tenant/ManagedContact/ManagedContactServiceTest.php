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
use App\Models\User;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;

it('creates a managed contact synchronized from the user', function () {
    $type = ContactType::factory()->create();

    $user = User::factory()->create([
        'name' => 'Jane Doe',
        'first_name' => 'Jane',
        'last_name' => 'Doe',
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

it('synchronizes every supported field from the user to the managed contact', function () {
    $type = ContactType::factory()->create();

    $user = User::factory()->create([
        'name' => 'Jane Doe',
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'preferred_name' => 'Janie',
        'email' => 'jane@example.com',
        'mobile' => '+1 555 333 4444',
        'employee_id' => 'EMP-1',
        'job_title' => 'Engineer',
        'work_number' => '+1 555 111 2222',
        'work_extension' => 4321,
        'student_id' => 'STU-1',
        'school' => 'School of Science',
        'academic_department' => 'Physics',
        'program' => 'PhD',
        'address' => '1 Main St',
        'address_2' => 'Suite 2',
        'city' => 'Springfield',
        'state' => 'IL',
        'postal_code' => '62701',
        'country' => 'US',
    ]);

    $contact = app(ManagedContactService::class)->enable($user, $type->getKey())->fresh();

    expect($contact->first_name)->toBe('Jane')
        ->and($contact->last_name)->toBe('Doe')
        ->and($contact->full_name)->toBe('Jane Doe')
        ->and($contact->preferred)->toBe('Janie')
        ->and($contact->type_id)->toBe($type->getKey())
        ->and($contact->email)->toBe('jane@example.com')
        ->and($contact->mobile)->toBe('+1 555 333 4444')
        ->and($contact->employee_id)->toBe('EMP-1')
        ->and($contact->job_title)->toBe('Engineer')
        ->and($contact->work_number)->toBe('+1 555 111 2222')
        ->and($contact->work_extension)->toBe('4321')
        ->and($contact->student_id)->toBe('STU-1')
        ->and($contact->school)->toBe('School of Science')
        ->and($contact->academic_department)->toBe('Physics')
        ->and($contact->program)->toBe('PhD')
        ->and($contact->address)->toBe('1 Main St')
        ->and($contact->address_2)->toBe('Suite 2')
        ->and($contact->city)->toBe('Springfield')
        ->and($contact->state)->toBe('IL')
        ->and($contact->postal)->toBe('62701')
        ->and($contact->country)->toBe('US');
});

it('updates the managed contact when any synchronized user field changes', function (string $userAttribute, string $contactAttribute) {
    $type = ContactType::factory()->create();

    $user = User::factory()->create();

    app(ManagedContactService::class)->enable($user, $type->getKey());

    $user->update([$userAttribute => 'Updated Value']);

    expect($user->managedContact()->first()->{$contactAttribute})->toBe('Updated Value');
})->with([
    'first name' => ['first_name', 'first_name'],
    'last name' => ['last_name', 'last_name'],
    'full name' => ['name', 'full_name'],
    'preferred name' => ['preferred_name', 'preferred'],
    'mobile' => ['mobile', 'mobile'],
    'employee id' => ['employee_id', 'employee_id'],
    'job title' => ['job_title', 'job_title'],
    'work number' => ['work_number', 'work_number'],
    'student id' => ['student_id', 'student_id'],
    'school' => ['school', 'school'],
    'academic department' => ['academic_department', 'academic_department'],
    'program' => ['program', 'program'],
    'address' => ['address', 'address'],
    'address 2' => ['address_2', 'address_2'],
    'city' => ['city', 'city'],
    'state' => ['state', 'state'],
    'postal' => ['postal_code', 'postal'],
    'country' => ['country', 'country'],
]);

it('updates the managed contact work extension when the user work extension changes', function () {
    $type = ContactType::factory()->create();

    $user = User::factory()->create(['work_extension' => 1111]);

    app(ManagedContactService::class)->enable($user, $type->getKey());

    $user->update(['work_extension' => 2222]);

    expect($user->managedContact()->first()->work_extension)->toBe('2222');
});

it('clears the managed contact values when the user values are cleared', function () {
    $type = ContactType::factory()->create();

    $user = User::factory()->create([
        'preferred_name' => 'Janie',
        'employee_id' => 'EMP-1',
        'work_extension' => 4321,
        'student_id' => 'STU-1',
        'school' => 'School of Science',
        'address' => '1 Main St',
        'country' => 'US',
    ]);

    app(ManagedContactService::class)->enable($user, $type->getKey());

    $user->update([
        'job_title' => null,
        'mobile' => null,
        'preferred_name' => '',
        'employee_id' => null,
        'work_number' => null,
        'work_extension' => null,
        'student_id' => null,
        'school' => null,
        'address' => null,
        'country' => '',
    ]);

    $contact = $user->managedContact()->first();

    expect($contact->job_title)->toBeNull()
        ->and($contact->mobile)->toBeNull()
        ->and($contact->phone)->toBeNull()
        ->and($contact->preferred)->toBeNull()
        ->and($contact->employee_id)->toBeNull()
        ->and($contact->work_number)->toBeNull()
        ->and($contact->work_extension)->toBeNull()
        ->and($contact->student_id)->toBeNull()
        ->and($contact->school)->toBeNull()
        ->and($contact->address)->toBeNull()
        ->and($contact->country)->toBeNull();
});

it('does not touch the managed contact when no synchronized user field changed', function () {
    $type = ContactType::factory()->create();

    $user = User::factory()->create();

    app(ManagedContactService::class)->enable($user, $type->getKey());

    $queries = [];

    DB::listen(function (QueryExecuted $query) use (&$queries): void {
        $queries[] = $query->sql;
    });

    $user->update(['last_activity_at' => now()]);

    expect(collect($queries)->filter(fn (string $sql): bool => str_contains($sql, '"contacts"')))->toBeEmpty();
});

it('synchronizes a stale managed contact from an already loaded user', function () {
    $type = ContactType::factory()->create();

    $user = User::factory()->create(['school' => 'School of Science', 'employee_id' => 'EMP-1']);

    $contact = app(ManagedContactService::class)->enable($user, $type->getKey());

    $contact->newQuery()->whereKey($contact->getKey())->update(['school' => null, 'employee_id' => null]);

    $contact = Contact::query()->with('managedByUser')->findOrFail($contact->getKey());

    app(ManagedContactService::class)->syncContact($contact, $contact->managedByUser);

    expect($contact->fresh()->school)->toBe('School of Science')
        ->and($contact->fresh()->employee_id)->toBe('EMP-1')
        ->and($contact->fresh()->type_id)->toBe($type->getKey());
});

it('synchronizes the managed contact when the user is updated', function () {
    $type = ContactType::factory()->create();

    $user = User::factory()->create(['name' => 'Old Name', 'job_title' => 'Junior']);

    app(ManagedContactService::class)->enable($user, $type->getKey());

    $user->update([
        'name' => 'Prof. New Name',
        'first_name' => 'New',
        'last_name' => 'Name',
        'job_title' => 'Senior',
        'email' => 'new-email@example.com',
        'work_number' => '+1 555 999 8888',
        'mobile' => '+1 555 777 6666',
    ]);

    $contact = $user->managedContact()->first();

    expect($contact->full_name)->toBe('Prof. New Name')
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
        'first_name' => 'New',
        'last_name' => 'Person',
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

it('links an existing contact whose email differs only in case instead of duplicating', function () {
    $type = ContactType::factory()->create();

    $existing = Contact::factory()->create([
        'email' => 'Match@Example.com',
        'first_name' => 'Old',
    ]);

    $user = User::factory()->create([
        'name' => 'New Person',
        'first_name' => 'New',
        'last_name' => 'Person',
        'email' => 'match@example.com',
    ]);

    $contact = app(ManagedContactService::class)->enable($user, $type->getKey());

    expect($contact->getKey())->toBe($existing->getKey())
        ->and($contact->user_id)->toBe($user->getKey())
        ->and($contact->first_name)->toBe('New')
        ->and($contact->type_id)->toBe($type->getKey());

    expect(Contact::query()->count())->toBe(1);
});

it('links an existing lower-case contact to a mixed-case user email instead of duplicating', function () {
    $type = ContactType::factory()->create();

    $existing = Contact::factory()->create(['email' => 'match@example.com']);

    $user = User::factory()->create(['email' => 'Match@Example.COM']);

    $contact = app(ManagedContactService::class)->enable($user, $type->getKey());

    expect($contact->getKey())->toBe($existing->getKey())
        ->and($contact->user_id)->toBe($user->getKey());

    expect(Contact::query()->count())->toBe(1);
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

it('does not reassign a contact managed by a different user', function () {
    $type = ContactType::factory()->create();

    $otherUser = User::factory()->create(['email' => 'other@example.com']);
    $otherContact = app(ManagedContactService::class)->enable($otherUser, $type->getKey());

    $user = User::factory()->create(['email' => 'new-user@example.com']);
    $contact = app(ManagedContactService::class)->enable($user, $type->getKey());

    expect($contact->getKey())->not->toBe($otherContact->getKey())
        ->and($otherContact->fresh()->user_id)->toBe($otherUser->getKey());
});

it('enforces a unique contact email case-insensitively', function () {
    Contact::factory()->create(['email' => 'shared@example.com']);

    expect(fn () => Contact::factory()->create(['email' => 'Shared@Example.com']))
        ->toThrow(UniqueConstraintViolationException::class);
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
