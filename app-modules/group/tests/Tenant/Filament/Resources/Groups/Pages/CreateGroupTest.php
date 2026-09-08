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

use AidingApp\Group\Filament\Resources\Groups\GroupResource;
use AidingApp\Group\Filament\Resources\Groups\Pages\CreateGroup;
use AidingApp\Group\Models\Group;
use AidingApp\Group\Tests\Tenant\Filament\Resources\Groups\RequestFactories\GroupRequestFactory;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('can render the create group page', function () {
    asSuperAdmin();

    get(GroupResource::getUrl('create'))->assertSuccessful();
});

it('can create a group', function () {
    $user = User::factory()->create();
    $user->givePermissionTo(['group.view-any', 'group.create']);
    actingAs($user);

    $request = GroupRequestFactory::new()->create();

    livewire(CreateGroup::class)
        ->fillForm($request)
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertRedirect();

    assertDatabaseHas(Group::class, [
        'name' => $request['name'],
        'description' => $request['description'],
        'created_by_id' => $user->getKey(),
    ]);
});

it('can create a group without a description', function () {
    asSuperAdmin();

    $request = GroupRequestFactory::new()->state(['description' => null])->create();

    livewire(CreateGroup::class)
        ->fillForm($request)
        ->call('create')
        ->assertHasNoFormErrors();

    assertDatabaseHas(Group::class, $request);
});

it('validates the inputs', function (GroupRequestFactory $data, array $errors) {
    asSuperAdmin();

    Group::factory()->create(['name' => 'Student Success']);

    livewire(CreateGroup::class)
        ->fillForm($data->create())
        ->call('create')
        ->assertHasFormErrors($errors);
})->with([
    'name required' => [GroupRequestFactory::new()->state(['name' => null]), ['name' => 'required']],
    'name max' => [GroupRequestFactory::new()->state(['name' => str()->random(256)]), ['name' => 'max']],
    'name unique case insensitive' => [GroupRequestFactory::new()->state(['name' => 'student success']), ['name' => 'unique']],
    'description max' => [GroupRequestFactory::new()->state(['description' => str()->random(65536)]), ['description' => 'max']],
]);

describe('authorization', function () {
    it('denies access without the `group.create` permission', function () {
        $user = User::factory()->create();
        $user->givePermissionTo('group.view-any');
        actingAs($user);

        get(GroupResource::getUrl('create'))->assertForbidden();
    });
});
