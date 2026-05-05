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
use AidingApp\ServiceManagement\Filament\Resources\Advisories\Pages\EditAdvisory;
use AidingApp\ServiceManagement\Models\Advisory;
use AidingApp\ServiceManagement\Tests\Tenant\RequestFactories\AdvisoryRequestFactory;
use App\Models\User;
use Filament\Actions\DeleteAction;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

test('EditAdvisory is gated with proper access control', function () {
    $user = User::factory()->create();

    $advisory = Advisory::factory()->create();

    actingAs($user)
        ->get(
            AdvisoryResource::getUrl('edit', [
                'record' => $advisory,
            ])
        )->assertForbidden();

    livewire(EditAdvisory::class, [
        'record' => $advisory->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('advisory.view-any');
    $user->givePermissionTo('advisory.*.update');

    actingAs($user)
        ->get(
            AdvisoryResource::getUrl('edit', [
                'record' => $advisory,
            ])
        )->assertSuccessful();

    $request = collect(AdvisoryRequestFactory::new()->create());

    livewire(EditAdvisory::class, [
        'record' => $advisory->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    expect($advisory->fresh()->title)->toEqual($request->get('title'))
        ->and($advisory->fresh()->description)->toEqual($request->get('description'))
        ->and($advisory->fresh()->severity_id)->toEqual($request->get('severity_id'))
        ->and($advisory->fresh()->status_id)->toEqual($request->get('status_id'))
        ->and($advisory->fresh()->assigned_team_id)->toEqual($request->get('assigned_team_id'));
});

test('EditAdvisory validates the inputs', function ($data, $errors) {
    $user = User::factory()->create();

    actingAs($user);

    $user->givePermissionTo('advisory.view-any');
    $user->givePermissionTo('advisory.*.update');

    $advisory = Advisory::factory()->create();

    $request = AdvisoryRequestFactory::new($data)->create();

    livewire(EditAdvisory::class, [
        'record' => $advisory->getRouteKey(),
    ])
        ->fillForm($request)
        ->call('save')
        ->assertHasFormErrors($errors);
})->with(
    [
        'title required' => [
            AdvisoryRequestFactory::new()->state(['title' => null]),
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
            AdvisoryRequestFactory::new()->state(['description' => null]),
            ['description' => 'required'],
        ],
        'description max' => [
            AdvisoryRequestFactory::new()->state(['description' => str()->random(65536)]),
            ['description' => 'max'],
        ],
        'severity_id missing' => [
            AdvisoryRequestFactory::new()->state(['severity_id' => null]),
            ['severity_id' => 'required'],
        ],
        'severity_id does not exist' => [
            AdvisoryRequestFactory::new()->state(['severity_id' => fake()->uuid()]),
            ['severity_id' => 'in'],
        ],
        'status_id missing' => [
            AdvisoryRequestFactory::new()->state(['status_id' => null]),
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

test('delete action visible with proper access control', function () {
    $user = User::factory()->create();

    $advisory = Advisory::factory()->create();

    actingAs($user);

    $user->givePermissionTo('advisory.view-any');
    $user->givePermissionTo('advisory.*.update');

    livewire(EditAdvisory::class, [
        'record' => $advisory->getRouteKey(),
    ])
        ->assertActionHidden(DeleteAction::class);

    $user->givePermissionTo('advisory.*.delete');

    livewire(EditAdvisory::class, [
        'record' => $advisory->getRouteKey(),
    ])
        ->assertActionVisible(DeleteAction::class);
});
