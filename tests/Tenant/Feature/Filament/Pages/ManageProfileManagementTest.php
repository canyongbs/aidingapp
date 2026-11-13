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

use App\Filament\Pages\ManageProfileManagement;
use App\Models\Pronouns;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('renders the personal pronouns tab', function (): void {
    $user = User::factory()->create();
    $user->givePermissionTo('settings.view-any');

    actingAs($user);

    Livewire::test(ManageProfileManagement::class)
        ->assertOk()
        ->assertSet('activeTab', 'personal-pronouns')
        ->assertSeeText('Personal Pronouns');
});

it('lists the existing pronouns', function (): void {
    $user = User::factory()->create();
    $user->givePermissionTo('settings.view-any');

    actingAs($user);

    $pronouns = Pronouns::factory()->create(['label' => 'She/Her']);

    Livewire::test(ManageProfileManagement::class)
        ->assertCanSeeTableRecords([$pronouns])
        ->assertSeeText('She/Her');
});

it('can create a pronoun', function (): void {
    $user = User::factory()->create();
    $user->givePermissionTo(['settings.view-any', 'settings.create']);

    actingAs($user);

    Livewire::test(ManageProfileManagement::class)
        ->callAction(TestAction::make('create')->table(), ['label' => 'They/Them'])
        ->assertHasNoActionErrors();

    expect(Pronouns::query()->where('label', 'They/Them')->exists())->toBeTrue();
});

it('can edit a pronoun', function (): void {
    $user = User::factory()->create();
    $user->givePermissionTo(['settings.view-any', 'settings.*.update']);

    actingAs($user);

    $pronoun = Pronouns::factory()->create(['label' => 'She/Her']);

    Livewire::test(ManageProfileManagement::class)
        ->callAction(TestAction::make('edit')->table($pronoun), ['label' => 'He/Him'])
        ->assertHasNoActionErrors();

    expect($pronoun->refresh()->label)->toBe('He/Him');
});

it('can delete a pronoun', function (): void {
    $user = User::factory()->create();
    $user->givePermissionTo(['settings.view-any', 'settings.*.delete']);

    actingAs($user);

    $pronoun = Pronouns::factory()->create();

    Livewire::test(ManageProfileManagement::class)
        ->callAction(TestAction::make('delete')->table($pronoun));

    expect(Pronouns::query()->whereKey($pronoun->getKey())->exists())->toBeFalse();
});

it('only shows the bulk delete action to a user with the settings.*.delete permission', function (): void {
    Pronouns::factory(3)->create();

    $user = User::factory()->create();
    $user->givePermissionTo(['settings.view-any', 'settings.*.view']);

    actingAs($user);

    Livewire::test(ManageProfileManagement::class)
        ->assertActionHidden(TestAction::make('delete')->table()->bulk());

    $user->givePermissionTo('settings.*.delete');

    Livewire::test(ManageProfileManagement::class)
        ->assertActionVisible(TestAction::make('delete')->table()->bulk());
});

describe('authorization', function (): void {
    it('denies access to the profile management page without the `settings.view-any` permission', function (): void {
        $user = User::factory()->create();

        actingAs($user);

        Livewire::test(ManageProfileManagement::class)->assertForbidden();
    });

    it('allows access to the profile management page with the `settings.view-any` permission', function (): void {
        $user = User::factory()->create();
        $user->givePermissionTo('settings.view-any');

        actingAs($user);

        Livewire::test(ManageProfileManagement::class)->assertOk();
    });
});
