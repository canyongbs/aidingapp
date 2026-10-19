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

use AidingApp\Department\Models\Department;
use AidingApp\ServiceManagement\Filament\Resources\Advisories\AdvisoryResource;
use AidingApp\ServiceManagement\Filament\Resources\Advisories\Pages\ListAdvisories;
use AidingApp\ServiceManagement\Models\Advisory;
use AidingApp\ServiceManagement\Models\AdvisorySeverity;
use AidingApp\ServiceManagement\Tests\Tenant\RequestFactories\AdvisoryRequestFactory;
use App\Models\User;
use App\Settings\LicenseSettings;
use CanyonGBS\Common\Enums\Color;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertSoftDeleted;
use function Pest\Laravel\get;
use function PHPUnit\Framework\assertCount;
use function Tests\asSuperAdmin;

test('ListAdvisories is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            AdvisoryResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('advisory.view-any');

    actingAs($user)
        ->get(
            AdvisoryResource::getUrl('index')
        )->assertSuccessful();
});

it('is gated with proper access control', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->advisoryManagement = false;
    $settings->save();

    $user = User::factory()->create();

    $user->givePermissionTo('advisory.view-any');

    actingAs($user);

    get(ListAdvisories::getUrl())->assertForbidden();

    $settings->data->addons->advisoryManagement = true;
    $settings->save();

    $user->revokePermissionTo('advisory.view-any');

    get(ListAdvisories::getUrl())->assertForbidden();

    $user->givePermissionTo('advisory.view-any');

    get(ListAdvisories::getUrl())->assertSuccessful();
});

test('can list records', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            AdvisoryResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('advisory.view-any');

    $records = Advisory::factory()->count(5)->create();

    Livewire::test(ListAdvisories::class)
        ->assertCountTableRecords(5)
        ->assertCanSeeTableRecords($records)
        ->assertSuccessful();
});

test('bulk delete Advisories', function () {
    $user = User::factory()->create();

    actingAs($user);

    $user->givePermissionTo('advisory.view-any');
    $user->givePermissionTo('advisory.*.delete');

    $advisories = Advisory::factory()->count(10)->create();

    Livewire::test(ListAdvisories::class)
        ->callTableBulkAction(DeleteBulkAction::class, $advisories);

    foreach ($advisories as $advisory) {
        assertSoftDeleted($advisory);
    }
});

it('only shows the bulk delete action to a user with the advisory.delete permission', function () {
    Advisory::factory(15)->create();

    $user = User::factory()
        ->create()
        ->givePermissionTo('advisory.view-any', 'advisory.*.view');

    actingAs($user);

    Livewire::test(ListAdvisories::class)
        ->assertActionHidden(TestAction::make('delete')->table()->bulk());

    $user->givePermissionTo('advisory.*.delete');

    Livewire::test(ListAdvisories::class)
        ->assertActionVisible(TestAction::make('delete')->table()->bulk());
});

it('links each row to the advisory view page', function () {
    $user = User::factory()
        ->create()
        ->givePermissionTo('advisory.view-any', 'advisory.*.view');

    actingAs($user);

    $advisory = Advisory::factory()->create();

    $url = Livewire::test(ListAdvisories::class)
        ->instance()
        ->getTable()
        ->getRecordUrl($advisory);

    expect($url)->toBe(AdvisoryResource::getUrl('view', ['record' => $advisory]));
});

it('does not link a row when the user cannot view the advisory', function () {
    $user = User::factory()
        ->create()
        ->givePermissionTo('advisory.view-any');

    actingAs($user);

    $advisory = Advisory::factory()->create();

    $url = Livewire::test(ListAdvisories::class)
        ->instance()
        ->getTable()
        ->getRecordUrl($advisory);

    expect($url)->toBeNull();
});

it('renders the severity column as a badge using the severity color', function () {
    $user = User::factory()
        ->create()
        ->givePermissionTo('advisory.view-any');

    actingAs($user);

    $advisory = Advisory::factory()
        ->for(AdvisorySeverity::factory()->color(Color::Red), 'severity')
        ->create();

    $column = Livewire::test(ListAdvisories::class)
        ->instance()
        ->getTable()
        ->getColumn('severity.name')
        ->record($advisory);

    expect($column->isBadge())->toBeTrue()
        ->and($column->getColor($advisory->severity->name))->toBe(Color::Red->value);
});

test('advisories can be created from the list page create modal with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user);

    $user->givePermissionTo('advisory.view-any');

    Livewire::test(ListAdvisories::class)
        ->assertSuccessful()
        ->assertActionHidden('create');

    $user->givePermissionTo('advisory.create');

    $request = AdvisoryRequestFactory::new()->create();

    Livewire::test(ListAdvisories::class)
        ->assertActionVisible('create')
        ->callAction('create', $request)
        ->assertHasNoActionErrors();

    assertCount(1, Advisory::all());

    assertDatabaseHas(Advisory::class, $request);
});

test('creating an advisory from the list page modal is gated by the advisory management feature', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->advisoryManagement = false;
    $settings->save();

    $user = User::factory()->create();

    actingAs($user);

    $user->givePermissionTo('advisory.view-any');
    $user->givePermissionTo('advisory.create');

    Livewire::test(ListAdvisories::class)
        ->assertForbidden();

    $settings->data->addons->advisoryManagement = true;
    $settings->save();

    $request = AdvisoryRequestFactory::new()->create();

    Livewire::test(ListAdvisories::class)
        ->assertSuccessful()
        ->callAction('create', $request)
        ->assertHasNoActionErrors();

    assertDatabaseHas(Advisory::class, $request);
});

test('the create advisory modal validates the inputs', function ($data, $errors) {
    asSuperAdmin();

    $request = AdvisoryRequestFactory::new($data)->create();

    Livewire::test(ListAdvisories::class)
        ->callAction('create', $request)
        ->assertHasActionErrors($errors);
})->with(
    [
        'title required' => [
            AdvisoryRequestFactory::new()->without('title'),
            ['title' => 'required'],
        ],
        'title string' => [
            AdvisoryRequestFactory::new()->state(['title' => 1]),
            ['title' => 'string'],
        ],
        'title max' => [
            AdvisoryRequestFactory::new()->state(['title' => str()->random(256)]),
            ['title' => 'max'],
        ],
        'description required' => [
            AdvisoryRequestFactory::new()->without('description'),
            ['description' => 'required'],
        ],
        'description max' => [
            AdvisoryRequestFactory::new()->state(['description' => str()->random(65536)]),
            ['description' => 'max'],
        ],
        'severity_id missing' => [
            AdvisoryRequestFactory::new()->without('severity_id'),
            ['severity_id' => 'required'],
        ],
        'severity_id does not exist' => [
            AdvisoryRequestFactory::new()->state(['severity_id' => fake()->uuid()]),
            ['severity_id' => 'in'],
        ],
        'status_id missing' => [
            AdvisoryRequestFactory::new()->without('status_id'),
            ['status_id' => 'required'],
        ],
        'status_id does not exist' => [
            AdvisoryRequestFactory::new()->state(['status_id' => fake()->uuid()]),
            ['status_id' => 'in'],
        ],
        'assigned_department_id does not exist' => [
            AdvisoryRequestFactory::new()->state(['assigned_department_id' => fake()->uuid()]),
            ['assigned_department_id' => 'in'],
        ],
    ]
);

test('creating an advisory redirects a user who can view it to the new advisory', function () {
    $user = User::factory()->create();

    actingAs($user);

    $user->givePermissionTo('advisory.view-any', 'advisory.create', 'advisory.*.view');

    $request = AdvisoryRequestFactory::new()->create();

    $component = Livewire::test(ListAdvisories::class)
        ->callAction('create', $request)
        ->assertHasNoActionErrors();

    $advisory = Advisory::query()->where('title', $request['title'])->sole();

    $component->assertRedirect(AdvisoryResource::getUrl('view', ['record' => $advisory]));
});

test('creating an advisory keeps a user who cannot view it on the list page', function () {
    $user = User::factory()->create();

    actingAs($user);

    $user->givePermissionTo('advisory.view-any', 'advisory.create');

    $request = AdvisoryRequestFactory::new()->create();

    Livewire::test(ListAdvisories::class)
        ->callAction('create', $request)
        ->assertHasNoActionErrors()
        ->assertNoRedirect();
});

test('the create advisory modal defaults the assigned department to the current user department', function () {
    $department = Department::factory()->create();

    $user = User::factory()->for($department)->create();

    actingAs($user);

    $user->givePermissionTo('advisory.view-any', 'advisory.create');

    Livewire::test(ListAdvisories::class)
        ->mountAction('create')
        ->assertSchemaStateSet(['assigned_department_id' => $department->getKey()]);
});
