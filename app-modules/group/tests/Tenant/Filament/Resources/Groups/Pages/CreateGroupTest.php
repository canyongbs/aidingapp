<?php

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
