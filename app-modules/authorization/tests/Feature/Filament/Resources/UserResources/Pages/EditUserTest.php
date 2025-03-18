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

use AidingApp\Authorization\Enums\LicenseType;
use App\Filament\Resources\UserResource;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\UserResource\RelationManagers\RolesRelationManager;
use App\Models\Authenticatable;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\AttachAction;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use STS\FilamentImpersonate\Pages\Actions\Impersonate;

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
        'permission.view-any',
        'permission.*.view',
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
        ->assertFormFieldExists('recordId', 'mountedTableActionForm', function (Select $select) {
            $options = $select->getSearchResults(Authenticatable::SUPER_ADMIN_ROLE);

            return empty($options);
        });
});

test('EditUser is gated with proper access control', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $anotherUser = User::factory()->create();

    actingAs($user)
        ->get(
            UserResource::getUrl('edit', [
                'record' => $anotherUser,
            ])
        )->assertForbidden();

    livewire(EditUser::class, [
        'record' => $anotherUser->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('user.view-any', 'user.*.view', 'user.*.update');

    actingAs($user)
        ->get(
            UserResource::getUrl('edit', [
                'record' => $anotherUser,
            ])
        )->assertSuccessful();

    $request = collect(User::factory()->make());

    livewire(EditUser::class, [
        'record' => $anotherUser->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    expect($anotherUser->fresh()->name)->toEqual($request->get('name'))
        ->and($anotherUser->fresh()->email)->toEqual($request->get('email'))
        ->and($anotherUser->fresh()->is_external)->toEqual($request->get('is_external'));
});

test('delete action visible with proper access control', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

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
    $user = User::factory()->licensed(LicenseType::cases())->create();

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
        'names required' => [
            ['name' => null],
            ['name' => 'required'],
        ],
        'name max' => [
            ['name' => str()->random(256)],
            ['name' => 'max'],
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
        'work_extension numeric' => [
            ['work_extension' => 'invalid'],
            ['work_extension' => 'numeric'],
        ],
        'job_title max' => [
            ['job_title' => str()->random(256)],
            ['job_title' => 'max'],
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
