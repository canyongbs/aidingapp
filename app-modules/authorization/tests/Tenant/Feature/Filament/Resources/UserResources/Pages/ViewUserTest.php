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
use App\Filament\Resources\Users\Pages\ViewUser;
use App\Filament\Resources\Users\UserResource;
use App\Models\Authenticatable;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use STS\FilamentImpersonate\Actions\Impersonate;

use function Tests\asSuperAdmin;

it('renders impersonate button for non super admin users when user is super admin', function () {
    asSuperAdmin();

    $user = User::factory()->create();

    livewire(ViewUser::class, [
        'record' => $user->getRouteKey(),
    ])
        ->assertSuccessful()
        ->assertActionVisible(Impersonate::class);
});

it('does not render impersonate button for super admin users at all', function () {
    $superAdmin = User::factory()->create();
    asSuperAdmin($superAdmin);

    $user = User::factory()->create();
    asSuperAdmin($user);

    livewire(ViewUser::class, [
        'record' => $superAdmin->getRouteKey(),
    ])
        ->assertSuccessful()
        ->assertActionHidden(Impersonate::class);
});

it('allows super admin user to impersonate', function () {
    $superAdmin = User::factory()->create();
    asSuperAdmin($superAdmin);

    $user = User::factory()->create();

    livewire(ViewUser::class, [
        'record' => $user->getRouteKey(),
    ])
        ->assertSuccessful()
        ->callAction(Impersonate::class);

    expect($user->isImpersonated())->toBeTrue();
    expect(auth()->id())->toBe($user->id);
});

test('ViewUser is gated with proper access control', function () {
    $user = User::factory()->create();

    $record = User::factory()->create();

    actingAs($user)
        ->get(
            UserResource::getUrl('view', ['record' => $record])
        )->assertForbidden();

    livewire(ViewUser::class, ['record' => $record->getKey()])
        ->assertForbidden();

    $user->givePermissionTo('user.view-any');
    $user->givePermissionTo('user.*.view');

    actingAs($user)
        ->get(
            UserResource::getUrl('view', ['record' => $record])
        )->assertSuccessful();

    livewire(ViewUser::class, ['record' => $record->getKey()])
        ->assertSuccessful();
});

test('ViewUser displays the record\'s current field values', function () {
    asSuperAdmin();

    $record = User::factory()->create([
        'first_name' => 'Jordan',
        'last_name' => 'Blake',
        'name' => 'Jordan Blake',
        'email' => 'jordan.blake@example.com',
    ]);

    livewire(ViewUser::class, ['record' => $record->getKey()])
        ->assertFormSet([
            'first_name' => 'Jordan',
            'last_name' => 'Blake',
            'name' => 'Jordan Blake',
            'email' => 'jordan.blake@example.com',
        ]);
});

test('ViewUser shows first and last name fields when the full name feature is active', function () {
    asSuperAdmin();

    $record = User::factory()->create();

    livewire(ViewUser::class, ['record' => $record->getKey()])
        ->assertFormFieldIsVisible('first_name')
        ->assertFormFieldIsVisible('last_name');
});

test('ViewUser disables all editable fields', function () {
    asSuperAdmin();

    $record = User::factory()->create();

    livewire(ViewUser::class, ['record' => $record->getKey()])
        ->assertFormFieldDisabled('first_name')
        ->assertFormFieldDisabled('last_name')
        ->assertFormFieldDisabled('name')
        ->assertFormFieldDisabled('email')
        ->assertFormFieldDisabled('job_title');
});

test('ViewUser hides the Department section for admin records', function () {
    asSuperAdmin();

    $record = User::factory()->create();
    $record->assignRole(Authenticatable::SUPER_ADMIN_ROLE);

    livewire(ViewUser::class, ['record' => $record->getKey()])
        ->assertFormFieldIsHidden('department_id');
});

test('ViewUser shows the Department section for non-admin records', function () {
    asSuperAdmin();

    $record = User::factory()->create();

    livewire(ViewUser::class, ['record' => $record->getKey()])
        ->assertFormFieldIsVisible('department_id');
});
