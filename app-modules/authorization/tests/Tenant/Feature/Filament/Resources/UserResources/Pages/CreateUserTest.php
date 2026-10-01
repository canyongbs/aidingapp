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

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use App\Notifications\SetPasswordNotification;
use Illuminate\Support\Facades\Notification;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

test('CreateUser is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            UserResource::getUrl('create')
        )->assertForbidden();

    livewire(CreateUser::class)
        ->assertForbidden();

    $user->givePermissionTo('user.view-any');
    $user->givePermissionTo('user.create');

    actingAs($user)
        ->get(
            UserResource::getUrl('create')
        )->assertSuccessful();

    livewire(CreateUser::class)
        ->fillForm([
            'first_name' => 'Jordan',
            'last_name' => 'Blake',
            'name' => 'Jordan Blake',
            'email' => 'jordan.blake@example.com',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    assertDatabaseHas(User::class, [
        'name' => 'Jordan Blake',
        'email' => 'jordan.blake@example.com',
    ]);
});

test('CreateUser shows and requires first and last name fields when the full name feature is active', function () {
    asSuperAdmin();

    livewire(CreateUser::class)
        ->assertFormFieldIsVisible('first_name')
        ->assertFormFieldIsVisible('last_name')
        ->fillForm([
            'first_name' => null,
            'last_name' => null,
            'email' => 'flagged@example.com',
        ])
        ->call('create')
        ->assertHasFormErrors([
            'first_name' => 'required',
            'last_name' => 'required',
        ]);
});

test('CreateUser requires an email address', function () {
    asSuperAdmin();

    livewire(CreateUser::class)
        ->fillForm([
            'first_name' => 'No',
            'last_name' => 'Email',
            'name' => 'No Email',
            'email' => null,
        ])
        ->call('create')
        ->assertHasFormErrors(['email' => 'required']);
});

test('CreateUser rejects an invalid email address', function () {
    asSuperAdmin();

    livewire(CreateUser::class)
        ->fillForm([
            'first_name' => 'Bad',
            'last_name' => 'Email',
            'name' => 'Bad Email',
            'email' => 'not-an-email',
        ])
        ->call('create')
        ->assertHasFormErrors(['email' => 'email']);
});

test('CreateUser rejects a duplicate email case-insensitively', function () {
    asSuperAdmin();

    User::factory()->create(['email' => 'taken@example.com']);

    livewire(CreateUser::class)
        ->fillForm([
            'first_name' => 'Duplicate',
            'last_name' => 'Email',
            'name' => 'Duplicate Email',
            'email' => 'Taken@Example.com',
        ])
        ->call('create')
        ->assertHasFormErrors(['email']);
});

test('CreateUser rejects reusing a soft-deleted user email', function () {
    asSuperAdmin();

    User::factory()->create(['email' => 'archived@example.com'])->delete();

    livewire(CreateUser::class)
        ->fillForm([
            'first_name' => 'Reuse',
            'last_name' => 'Attempt',
            'name' => 'Reuse Attempt',
            'email' => 'Archived@Example.com',
        ])
        ->call('create')
        ->assertHasFormErrors(['email']);

    expect(User::withTrashed()->where('email', 'archived@example.com')->count())->toBe(1);
});

test('CreateUser sends the set password notification to a non-external user', function () {
    asSuperAdmin();

    Notification::fake();

    livewire(CreateUser::class)
        ->fillForm([
            'first_name' => 'Internal',
            'last_name' => 'User',
            'name' => 'Internal User',
            'email' => 'internal@example.com',
            'is_external' => false,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $created = User::query()->where('email', 'internal@example.com')->firstOrFail();

    Notification::assertSentTo($created, SetPasswordNotification::class);
});

test('CreateUser does not send the set password notification to an external user', function () {
    asSuperAdmin();

    Notification::fake();

    livewire(CreateUser::class)
        ->fillForm([
            'first_name' => 'External',
            'last_name' => 'User',
            'name' => 'External User',
            'email' => 'external@example.com',
            'is_external' => true,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $created = User::query()->where('email', 'external@example.com')->firstOrFail();

    Notification::assertNotSentTo($created, SetPasswordNotification::class);
});

test('CreateUser validates the inputs', function ($data, $errors) {
    asSuperAdmin();

    $request = User::factory()->state($data)->make()->toArray();

    livewire(CreateUser::class)
        ->fillForm($request)
        ->call('create')
        ->assertHasFormErrors($errors);
})->with(
    [
        'first name required' => [
            ['first_name' => null],
            ['first_name' => 'required'],
        ],
        'first_name max' => [
            ['first_name' => str()->random(256)],
            ['first_name' => 'max'],
        ],
        'last name required' => [
            ['last_name' => null],
            ['last_name' => 'required'],
        ],
        'last_name max' => [
            ['last_name' => str()->random(256)],
            ['last_name' => 'max'],
        ],
        'preferred_name max' => [
            ['preferred_name' => str()->random(256)],
            ['preferred_name' => 'max'],
        ],
        'employee_id max' => [
            ['employee_id' => str()->random(256)],
            ['employee_id' => 'max'],
        ],
        'job_title max' => [
            ['job_title' => str()->random(256)],
            ['job_title' => 'max'],
        ],
        'work_extension numeric' => [
            ['work_extension' => 'invalid'],
            ['work_extension' => 'numeric'],
        ],
        'student_id max' => [
            ['student_id' => str()->random(256)],
            ['student_id' => 'max'],
        ],
        'school max' => [
            ['school' => str()->random(256)],
            ['school' => 'max'],
        ],
        'academic_department max' => [
            ['academic_department' => str()->random(256)],
            ['academic_department' => 'max'],
        ],
        'program max' => [
            ['program' => str()->random(256)],
            ['program' => 'max'],
        ],
        'email required' => [
            ['email' => null],
            ['email' => 'required'],
        ],
        'email max' => [
            ['email' => str()->random(256) . '@example.com'],
            ['email' => 'max'],
        ],
        'email valid' => [
            ['email' => 'invalidEmail'],
            ['email' => 'email'],
        ],
        'address max' => [
            ['address' => str()->random(256)],
            ['address' => 'max'],
        ],
        'address_2 max' => [
            ['address_2' => str()->random(256)],
            ['address_2' => 'max'],
        ],
        'city max' => [
            ['city' => str()->random(256)],
            ['city' => 'max'],
        ],
        'state max' => [
            ['state' => str()->random(256)],
            ['state' => 'max'],
        ],
        'postal_code max' => [
            ['postal_code' => str()->random(256)],
            ['postal_code' => 'max'],
        ],
        'country max' => [
            ['country' => str()->random(256)],
            ['country' => 'max'],
        ],
        'managed_contact_type_id required when managed contact enabled' => [
            ['is_managed_contact' => true, 'managed_contact_type_id' => null],
            ['managed_contact_type_id' => 'required'],
        ],
    ]
);
