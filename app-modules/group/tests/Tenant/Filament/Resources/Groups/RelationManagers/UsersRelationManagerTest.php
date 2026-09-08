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

use AidingApp\Group\Filament\Resources\Groups\Pages\EditGroup;
use AidingApp\Group\Filament\Resources\Groups\RelationManagers\UsersRelationManager;
use AidingApp\Group\Models\Group;
use App\Models\User;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\Testing\TestAction;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('can associate multiple users with a group', function () {
    asSuperAdmin();
    $group = Group::factory()->create();
    $users = User::factory()->count(3)->create();

    livewire(UsersRelationManager::class, [
        'ownerRecord' => $group,
        'pageClass' => EditGroup::class,
    ])
        ->callAction(TestAction::make(AttachAction::class)->table(), data: [
            'recordId' => $users->modelKeys(),
        ])
        ->assertHasNoFormErrors();

    expect($group->users()->pluck('users.id')->all())
        ->toEqualCanonicalizing($users->modelKeys());
});

it('can detach a user from a group', function () {
    asSuperAdmin();
    $group = Group::factory()->create();
    $user = User::factory()->create();
    $group->users()->attach($user);

    livewire(UsersRelationManager::class, [
        'ownerRecord' => $group,
        'pageClass' => EditGroup::class,
    ])->callAction(TestAction::make(DetachAction::class)->table($user));

    expect($group->users()->whereKey($user->getKey())->exists())->toBeFalse();
});

it('can bulk detach users from a group', function () {
    asSuperAdmin();
    $group = Group::factory()->create();
    $users = User::factory()->count(2)->create();
    $group->users()->attach($users);

    livewire(UsersRelationManager::class, [
        'ownerRecord' => $group,
        'pageClass' => EditGroup::class,
    ])
        ->selectTableRecords($users->modelKeys())
        ->callAction(TestAction::make(DetachBulkAction::class)->table()->bulk());

    expect($group->users()->exists())->toBeFalse();
});

describe('authorization', function () {
    it('hides membership actions without the `group.*.update` permission', function () {
        $user = User::factory()->create();
        $user->givePermissionTo(['group.view-any', 'group.*.view']);
        actingAs($user);
        $group = Group::factory()->create();

        livewire(UsersRelationManager::class, [
            'ownerRecord' => $group,
            'pageClass' => EditGroup::class,
        ])->assertActionHidden(TestAction::make(AttachAction::class)->table());
    });
});
