<?php

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
