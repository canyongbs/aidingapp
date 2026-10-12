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

use AidingApp\ServiceManagement\Filament\Resources\Advisories\Pages\ListAdvisories;
use AidingApp\ServiceManagement\Models\Advisory;
use AidingApp\ServiceManagement\Tests\Tenant\RequestFactories\AdvisoryRequestFactory;
use App\Models\User;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertCount;
use function Tests\asSuperAdmin;

test('advisories can be created from the list page create modal with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user);

    $user->givePermissionTo('advisory.view-any');

    livewire(ListAdvisories::class)
        ->assertSuccessful()
        ->assertActionDisabled('create');

    $user->givePermissionTo('advisory.create');

    $request = AdvisoryRequestFactory::new()->create();

    livewire(ListAdvisories::class)
        ->assertActionEnabled('create')
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

    livewire(ListAdvisories::class)
        ->assertForbidden();

    $settings->data->addons->advisoryManagement = true;
    $settings->save();

    $request = AdvisoryRequestFactory::new()->create();

    livewire(ListAdvisories::class)
        ->assertSuccessful()
        ->callAction('create', $request)
        ->assertHasNoActionErrors();

    assertDatabaseHas(Advisory::class, $request);
});

test('the create advisory modal validates the inputs', function ($data, $errors) {
    asSuperAdmin();

    $request = AdvisoryRequestFactory::new($data)->create();

    livewire(ListAdvisories::class)
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
