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

use AidingApp\Team\Filament\Resources\TeamResource;
use AidingApp\Team\Filament\Resources\TeamResource\Pages\EditTeam;
use AidingApp\Team\Models\Team;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

// Permission Tests

test('EditTeam is gated with proper access control', function () {
    $user = User::factory()->create();

    $team = Team::factory()->create();

    actingAs($user)
        ->get(
            TeamResource::getUrl('edit', [
                'record' => $team,
            ])
        )->assertForbidden();

    livewire(EditTeam::class, [
        'record' => $team->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('team.view-any');
    $user->givePermissionTo('team.*.update');

    actingAs($user)
        ->get(
            TeamResource::getUrl('edit', [
                'record' => $team,
            ])
        )->assertSuccessful();

    // TODO: Finish these tests to ensure changes are allowed
    /** @var Team $request */
    $request = Team::factory()->make();

    livewire(EditTeam::class, [
        'record' => $team->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    $team->refresh();

    expect($team->name)->toEqual($request->name)
        ->and($team->description)->toEqual($request->description);
});
