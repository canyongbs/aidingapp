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

use AidingApp\Authorization\Filament\Resources\Roles\Pages\ViewRole;
use AidingApp\Authorization\Filament\Resources\Roles\RoleResource;
use AidingApp\Authorization\Models\Role;
use App\Models\Authenticatable;
use App\Models\User;
use Filament\Actions\EditAction;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

dataset('admins', [
    'super admin' => fn () => user(Authenticatable::SUPER_ADMIN_ROLE),
    'partner admin' => fn () => user(Authenticatable::PARTNER_ADMIN_ROLE),
]);

dataset('admin roles', [
    'SaaS Global Admin role' => Authenticatable::SUPER_ADMIN_ROLE,
    'Partner Admin role' => Authenticatable::PARTNER_ADMIN_ROLE,
    'AI Admin role' => Authenticatable::AI_ADMIN_ROLE,
]);

describe('authorization', function () {
    it('denies access to the view role page without the `role.*.view` permission', function () {
        $user = User::factory()->create();
        $user->givePermissionTo('role.view-any');
        actingAs($user);

        $role = Role::factory()->create();

        get(RoleResource::getUrl('view', ['record' => $role]))
            ->assertForbidden();
    });

    it('allows access to the view role page with the `role.*.view` permission', function () {
        $user = User::factory()->create();
        $user->givePermissionTo('role.view-any', 'role.*.view');
        actingAs($user);

        $role = Role::factory()->create();

        get(RoleResource::getUrl('view', ['record' => $role]))
            ->assertSuccessful();
    });

    it('allows a super admin to access the view role page', function () {
        asSuperAdmin();

        $role = Role::factory()->create();

        get(RoleResource::getUrl('view', ['record' => $role]))
            ->assertSuccessful();
    });

    it('allows a partner admin to access the view role page without any explicit permissions', function () {
        actingAs(user(Authenticatable::PARTNER_ADMIN_ROLE));

        $role = Role::factory()->create();

        get(RoleResource::getUrl('view', ['record' => $role]))
            ->assertSuccessful();
    });

    it('shows the `EditAction` action with the `role.*.update` permission', function () {
        $user = User::factory()->create();
        $user->givePermissionTo('role.view-any', 'role.*.view', 'role.*.update');
        actingAs($user);

        $role = Role::factory()->create();

        livewire(ViewRole::class, ['record' => $role->getRouteKey()])
            ->assertActionVisible(EditAction::class);
    });

    it('hides the `EditAction` action on an admin role for an admin', function (User $admin, string $roleName) {
        actingAs($admin);

        $role = Role::query()
            ->where('name', $roleName)
            ->where('guard_name', 'web')
            ->firstOrFail();

        livewire(ViewRole::class, ['record' => $role->getRouteKey()])
            ->assertActionHidden(EditAction::class);
    })->with('admins')->with('admin roles');
});
