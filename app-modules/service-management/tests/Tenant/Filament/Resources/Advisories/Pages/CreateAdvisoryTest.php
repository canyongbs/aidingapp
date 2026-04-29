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

use AidingApp\ServiceManagement\Filament\Resources\Advisories\AdvisoryResource;
use AidingApp\ServiceManagement\Filament\Resources\Advisories\Pages\CreateAdvisory;
use AidingApp\ServiceManagement\Models\Advisory;
use AidingApp\ServiceManagement\Tests\Tenant\RequestFactories\AdvisoryRequestFactory;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertCount;
use function Tests\asSuperAdmin;

test('CreateAdvisory is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            AdvisoryResource::getUrl('create')
        )->assertForbidden();

    livewire(CreateAdvisory::class)
        ->assertForbidden();

    $user->givePermissionTo('advisory.view-any');
    $user->givePermissionTo('advisory.create');

    actingAs($user)
        ->get(
            AdvisoryResource::getUrl('create')
        )->assertSuccessful();

    $request = AdvisoryRequestFactory::new()->create();

    livewire(CreateAdvisory::class)
        ->fillForm($request)
        ->call('create')
        ->assertHasNoFormErrors();

    assertCount(1, Advisory::all());

    assertDatabaseHas(Advisory::class, $request);
});

test('CreateAdvisory validates the inputs', function ($data, $errors) {
    asSuperAdmin();

    $request = AdvisoryRequestFactory::new($data)->create();

    livewire(CreateAdvisory::class)
        ->fillForm($request)
        ->call('create')
        ->assertHasFormErrors($errors);
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
        'assigned_team_id does not exist' => [
            AdvisoryRequestFactory::new()->state(['assigned_team_id' => fake()->uuid()]),
            ['assigned_team_id' => 'in'],
        ],
    ]
);
