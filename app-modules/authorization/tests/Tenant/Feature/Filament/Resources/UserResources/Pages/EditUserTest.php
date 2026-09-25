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
use App\Features\FullNameFeature;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\RelationManagers\RolesRelationManager;
use App\Filament\Resources\Users\UserResource;
use App\Models\Authenticatable;
use App\Models\User;
use Filament\Actions\AttachAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;

use STS\FilamentImpersonate\Actions\Impersonate;

use function Tests\asSuperAdmin;

it('renders impersonate button for non super admin users when user is super admin', function () {
    asSuperAdmin();

    $user = User::factory()->create();

    $component = livewire(EditUser::class, [
        'record' => $user->getRouteKey(),
    ]);

    $component
        ->assertSuccessful()
        ->assertActionVisible(Impersonate::class);
});

it('does not render impersonate button for super admin users at all', function () {
    $superAdmin = User::factory()->create();
    asSuperAdmin($superAdmin);

    $user = User::factory()->create();
    asSuperAdmin($user);

    $component = livewire(EditUser::class, [
        'record' => $superAdmin->getRouteKey(),
    ]);

    $component
        ->assertSuccessful()
        ->assertActionHidden(Impersonate::class);
});

it('allows super admin user to impersonate', function () {
    $superAdmin = User::factory()->create();
    asSuperAdmin($superAdmin);

    $user = User::factory()->create();

    $component = livewire(EditUser::class, [
        'record' => $user->getRouteKey(),
    ]);

    $component
        ->assertSuccessful()
        ->callAction(Impersonate::class);

    expect($user->isImpersonated())->toBeTrue();
    expect(auth()->id())->toBe($user->id);
});

it('allows user with permission to impersonate', function () {
    $first = User::factory()->create();
    $first->givePermissionTo('user.view-any', 'user.*.view', 'user.*.update');
    asSuperAdmin($first);

    $second = User::factory()->create();

    $component = livewire(EditUser::class, [
        'record' => $second->getRouteKey(),
    ]);

    $component
        ->assertSuccessful()
        ->callAction(Impersonate::class);

    expect($second->isImpersonated())->toBeTrue();
    expect(auth()->id())->toBe($second->id);
});

it('does not allow a user which does not have the SaaS Global Admin role to assign SaaS Global Admin role to other users', function () {
    $user = User::factory()->create();
    $user->givePermissionTo(
        'role.view-any',
        'role.*.view',
        'user.view-any',
        'user.*.view',
        'user.create',
        'user.*.update',
        'user.*.delete',
        'user.*.restore',
        'user.*.force-delete',
    );

    $second = User::factory()->create();

    actingAs($user)
        ->get(
            UserResource::getUrl('edit', [
                'record' => $second,
            ])
        )->assertSuccessful();

    livewire(RolesRelationManager::class, [
        'ownerRecord' => $second,
        'pageClass' => EditUser::class,
    ])
        ->mountTableAction(AttachAction::class)
        ->assertFormFieldExists('recordId', 'mountedActionSchema0', function (Select $select) {
            $options = $select->getSearchResults(Authenticatable::SUPER_ADMIN_ROLE);

            return empty($options);
        });
});

test('EditUser is gated with proper access control', function () {
    $user = User::factory()->create();

    $record = User::factory()->create();

    actingAs($user)
        ->get(
            UserResource::getUrl('edit', ['record' => $record])
        )->assertForbidden();

    livewire(EditUser::class, ['record' => $record->getKey()])
        ->assertForbidden();

    $user->givePermissionTo('user.view-any');
    $user->givePermissionTo('user.*.view');
    $user->givePermissionTo('user.*.update');

    actingAs($user)
        ->get(
            UserResource::getUrl('edit', ['record' => $record])
        )->assertSuccessful();

    livewire(EditUser::class, ['record' => $record->getKey()])
        ->fillForm([
            'first_name' => 'Jordan',
            'last_name' => 'Blake',
            'name' => 'Jordan Blake',
            'email' => 'jordan.blake@example.com',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    assertDatabaseHas(User::class, [
        'id' => $record->getKey(),
        'name' => 'Jordan Blake',
        'email' => 'jordan.blake@example.com',
    ]);
});

test('EditUser requires an email address', function () {
    asSuperAdmin();

    $record = User::factory()->create();

    livewire(EditUser::class, ['record' => $record->getKey()])
        ->fillForm([
            'first_name' => 'No',
            'last_name' => 'Email',
            'name' => 'No Email',
            'email' => null,
        ])
        ->call('save')
        ->assertHasFormErrors(['email' => 'required']);
});

test('EditUser rejects an invalid email address', function () {
    asSuperAdmin();

    $record = User::factory()->create();

    livewire(EditUser::class, ['record' => $record->getKey()])
        ->fillForm([
            'first_name' => 'Bad',
            'last_name' => 'Email',
            'name' => 'Bad Email',
            'email' => 'not-an-email',
        ])
        ->call('save')
        ->assertHasFormErrors(['email' => 'email']);
});

test('EditUser allows saving with its own unchanged email address', function () {
    asSuperAdmin();

    $record = User::factory()->create(['email' => 'unchanged@example.com']);

    livewire(EditUser::class, ['record' => $record->getKey()])
        ->fillForm([
            'first_name' => 'Still',
            'last_name' => 'Here',
            'name' => 'Still Here',
            'email' => 'unchanged@example.com',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    assertDatabaseHas(User::class, [
        'id' => $record->getKey(),
        'email' => 'unchanged@example.com',
    ]);
});

test('EditUser rejects a duplicate email belonging to another user case-insensitively', function () {
    asSuperAdmin();

    User::factory()->create(['email' => 'taken@example.com']);

    $record = User::factory()->create();

    livewire(EditUser::class, ['record' => $record->getKey()])
        ->fillForm([
            'first_name' => 'Duplicate',
            'last_name' => 'Email',
            'name' => 'Duplicate Email',
            'email' => 'Taken@Example.com',
        ])
        ->call('save')
        ->assertHasFormErrors(['email']);
});

test('EditUser rejects reusing another soft-deleted user\'s email', function () {
    asSuperAdmin();

    User::factory()->create(['email' => 'archived@example.com'])->delete();

    $record = User::factory()->create();

    livewire(EditUser::class, ['record' => $record->getKey()])
        ->fillForm([
            'first_name' => 'Reuse',
            'last_name' => 'Attempt',
            'name' => 'Reuse Attempt',
            'email' => 'Archived@Example.com',
        ])
        ->call('save')
        ->assertHasFormErrors(['email']);
});

test('EditUser shows and requires first and last name fields when the full name feature is active', function () {
    asSuperAdmin();

    $record = User::factory()->create();

    livewire(EditUser::class, ['record' => $record->getKey()])
        ->assertFormFieldIsVisible('first_name')
        ->assertFormFieldIsVisible('last_name')
        ->fillForm([
            'first_name' => null,
            'last_name' => null,
        ])
        ->call('save')
        ->assertHasFormErrors([
            'first_name' => 'required',
            'last_name' => 'required',
        ]);
});

test('EditUser disables demographic and contact fields when editing an admin record', function () {
    asSuperAdmin();

    $record = User::factory()->create();
    $record->assignRole(Authenticatable::SUPER_ADMIN_ROLE);

    livewire(EditUser::class, ['record' => $record->getKey()])
        ->assertFormFieldDisabled('first_name')
        ->assertFormFieldDisabled('last_name')
        ->assertFormFieldDisabled('name')
        ->assertFormFieldDisabled('job_title')
        ->assertFormFieldDisabled('email')
        ->assertFormFieldDisabled('is_external');
});

test('EditUser disables the full name field when editing an admin record even if the full name feature is inactive', function () {
    asSuperAdmin();

    FullNameFeature::deactivate();

    $record = User::factory()->create();
    $record->assignRole(Authenticatable::SUPER_ADMIN_ROLE);

    livewire(EditUser::class, ['record' => $record->getKey()])
        ->assertFormFieldDisabled('name');
});

test('EditUser enables demographic and contact fields when editing a non-admin record', function () {
    asSuperAdmin();

    $record = User::factory()->create();

    livewire(EditUser::class, ['record' => $record->getKey()])
        ->assertFormFieldEnabled('first_name')
        ->assertFormFieldEnabled('last_name')
        ->assertFormFieldEnabled('job_title')
        ->assertFormFieldEnabled('email')
        ->assertFormFieldEnabled('is_external');
});

test('delete action visible with proper access control', function () {
    $user = User::factory()->create();

    $anotherUser = User::factory()->create();

    actingAs($user);

    $user->givePermissionTo('user.view-any');
    $user->givePermissionTo('user.*.update');

    livewire(EditUser::class, [
        'record' => $anotherUser->getRouteKey(),
    ])
        ->assertActionHidden(DeleteAction::class);

    $user->givePermissionTo('user.*.delete');

    livewire(EditUser::class, [
        'record' => $anotherUser->getRouteKey(),
    ])
        ->assertActionVisible(DeleteAction::class);
});

test('EditUser validates the inputs', function ($data, $errors) {
    $user = User::factory()->create();

    actingAs($user);

    $user->givePermissionTo('user.view-any');
    $user->givePermissionTo('user.*.update');

    $anotherUser = User::factory()->create();

    $request = User::factory()->state($data)->make()->toArray();

    livewire(EditUser::class, [
        'record' => $anotherUser->getRouteKey(),
    ])
        ->fillForm($request)
        ->call('save')
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

it('prevents assigning an email that belongs to a soft-deleted user', function () {
    $user = User::factory()->create();
    $deletedUser = User::factory()->state(['deleted_at' => now()])->create();

    actingAs($user);

    $user->givePermissionTo('user.view-any');
    $user->givePermissionTo('user.*.update');

    $request = User::factory()->make(['email' => $deletedUser->email])->toArray();

    livewire(EditUser::class, [
        'record' => $user->getRouteKey(),
    ])
        ->fillForm($request)
        ->call('save')
        ->assertHasFormErrors(['email' => 'An archived user with this email address already exists. Please contact an administrator to restore this user or use a different email address.']);
});

it('prevents assigning duplicate email to a user', function () {
    $user = User::factory()->create();
    $anotherUser = User::factory()->create();

    actingAs($user);

    $user->givePermissionTo('user.view-any');
    $user->givePermissionTo('user.*.update');

    $request = User::factory()->make(['email' => $anotherUser->email])->toArray();

    livewire(EditUser::class, [
        'record' => $user->getRouteKey(),
    ])
        ->fillForm($request)
        ->call('save')
        ->assertHasFormErrors(['email' => 'A user with this email address already exists. Please use a different email address or contact your administrator if you need to modify this user\'s account.']);
});
