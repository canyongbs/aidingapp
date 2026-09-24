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

use AidingApp\Group\Models\Group;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\RelationManagers\GroupsRelationManager;
use App\Models\User;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\Testing\TestAction;
use Filament\Forms\Components\Select;

use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('can associate multiple groups with a user', function () {
    asSuperAdmin();

    $user = User::factory()->create();
    $groups = Group::factory()->count(3)->create();

    livewire(GroupsRelationManager::class, [
        'ownerRecord' => $user,
        'pageClass' => EditUser::class,
    ])
        ->callAction(TestAction::make(AttachAction::class)->table(), data: [
            'recordId' => $groups->modelKeys(),
        ])
        ->assertHasNoFormErrors();

    expect($user->groups()->pluck('groups.id')->all())
        ->toEqualCanonicalizing($groups->modelKeys());
});

it('can detach a group from a user', function () {
    asSuperAdmin();

    $user = User::factory()->create();
    $group = Group::factory()->create();
    $user->groups()->attach($group);

    livewire(GroupsRelationManager::class, [
        'ownerRecord' => $user,
        'pageClass' => EditUser::class,
    ])
        ->callAction(TestAction::make(DetachAction::class)->table($group));

    expect($user->groups()->pluck('groups.id')->all())->not->toContain($group->getKey());
});

it('excludes archived groups from the associate groups selection', function () {
    asSuperAdmin();

    $user = User::factory()->create();
    $activeGroup = Group::factory()->create();
    $archivedGroup = Group::factory()->create(['archived_at' => now()]);

    $component = livewire(GroupsRelationManager::class, [
        'ownerRecord' => $user,
        'pageClass' => EditUser::class,
    ]);

    $component->mountAction(TestAction::make(AttachAction::class)->table());

    $recordSelect = $component->instance()->getMountedTableActionForm()?->getComponent('recordId');

    assert($recordSelect instanceof Select);

    expect($recordSelect->getOptions())->toHaveKey($activeGroup->getKey());
    expect($recordSelect->getOptions())->not->toHaveKey($archivedGroup->getKey());
});
