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

use AidingApp\ServiceManagement\Filament\Resources\AdvisorySeverities\AdvisorySeverityResource;
use AidingApp\ServiceManagement\Filament\Resources\AdvisorySeverities\Pages\EditAdvisorySeverity;
use AidingApp\ServiceManagement\Models\AdvisorySeverity;
use AidingApp\ServiceManagement\Tests\Tenant\RequestFactories\AdvisorySeverityRequestFactory;
use App\Features\AdvisorySeverityNameUniquenessFeature;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Illuminate\Database\UniqueConstraintViolationException;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Tests\asSuperAdmin;

test('EditAdvisorySeverity is gated with proper access control', function (): void {
    $user = User::factory()->create();

    $advisorySeverity = AdvisorySeverity::factory()->create();

    actingAs($user)
        ->get(
            AdvisorySeverityResource::getUrl('edit', [
                'record' => $advisorySeverity,
            ])
        )->assertForbidden();

    Livewire::test(EditAdvisorySeverity::class, [
        'record' => $advisorySeverity->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('settings.view-any');
    $user->givePermissionTo('settings.*.update');

    actingAs($user)
        ->get(
            AdvisorySeverityResource::getUrl('edit', [
                'record' => $advisorySeverity,
            ])
        )->assertSuccessful();

    $request = collect(AdvisorySeverityRequestFactory::new()->create());

    Livewire::test(EditAdvisorySeverity::class, [
        'record' => $advisorySeverity->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    expect($advisorySeverity->fresh()->name)->toEqual($request->get('name'));
});

test('EditAdvisorySeverity validates the inputs', function (AdvisorySeverityRequestFactory $data, array $errors): void {
    $user = User::factory()->create();

    actingAs($user);

    $user->givePermissionTo('settings.view-any');
    $user->givePermissionTo('settings.*.update');

    AdvisorySeverity::factory()->create(['name' => 'Critical']);
    $advisorySeverity = AdvisorySeverity::factory()->create();

    $request = AdvisorySeverityRequestFactory::new($data)->create();

    Livewire::test(EditAdvisorySeverity::class, [
        'record' => $advisorySeverity->getRouteKey(),
    ])
        ->fillForm($request)
        ->call('save')
        ->assertHasFormErrors($errors);
})->with(
    [
        'name required' => [
            AdvisorySeverityRequestFactory::new()->state(['name' => null]),
            ['name' => 'required'],
        ],
        'name string' => [
            AdvisorySeverityRequestFactory::new()->state(['name' => 1]),
            ['name' => 'string'],
        ],
        'name max' => [
            AdvisorySeverityRequestFactory::new()->state(['name' => str()->random(256)]),
            ['name' => 'max'],
        ],
        'color required' => [
            AdvisorySeverityRequestFactory::new()->state(['color' => null]),
            ['color' => 'required'],
        ],
        'name unique' => [
            AdvisorySeverityRequestFactory::new()->state(['name' => 'Critical']),
            ['name' => 'unique'],
        ],
        'name unique case insensitive' => [
            AdvisorySeverityRequestFactory::new()->state(['name' => 'CRITICAL']),
            ['name' => 'unique'],
        ],
    ]
);

test('delete action visible with proper access control', function (): void {
    $user = User::factory()->create();

    $advisorySeverity = AdvisorySeverity::factory()->create();

    actingAs($user);

    $user->givePermissionTo('settings.view-any');
    $user->givePermissionTo('settings.*.update');

    Livewire::test(EditAdvisorySeverity::class, [
        'record' => $advisorySeverity->getRouteKey(),
    ])
        ->assertActionHidden(DeleteAction::class);

    $user->givePermissionTo('settings.*.delete');

    Livewire::test(EditAdvisorySeverity::class, [
        'record' => $advisorySeverity->getRouteKey(),
    ])
        ->assertActionVisible(DeleteAction::class);
});

test('EditAdvisorySeverity allows keeping its own name with a different case', function (): void {
    asSuperAdmin();

    $advisorySeverity = AdvisorySeverity::factory()->create(['name' => 'Critical']);

    Livewire::test(EditAdvisorySeverity::class, [
        'record' => $advisorySeverity->getRouteKey(),
    ])
        ->fillForm(['name' => 'CRITICAL'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($advisorySeverity->refresh()->name)->toBe('CRITICAL');
});

test('EditAdvisorySeverity allows the name of a soft deleted advisory severity', function (): void {
    asSuperAdmin();

    AdvisorySeverity::factory()->create(['name' => 'Critical'])->delete();
    $advisorySeverity = AdvisorySeverity::factory()->create(['name' => 'Minor']);

    Livewire::test(EditAdvisorySeverity::class, [
        'record' => $advisorySeverity->getRouteKey(),
    ])
        ->fillForm(['name' => 'Critical'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($advisorySeverity->refresh()->name)->toBe('Critical');
});

// TODO: Cleanup Task (advisory-status-and-severity-name-uniqueness): delete this describe and the test within
describe('name uniqueness feature flag', function (): void {
    it('does not apply the unique form rule when the feature is disabled', function (): void {
        AdvisorySeverityNameUniquenessFeature::deactivate();

        asSuperAdmin();

        AdvisorySeverity::factory()->create(['name' => 'Critical']);
        $advisorySeverity = AdvisorySeverity::factory()->create(['name' => 'Minor']);

        expect(fn () => Livewire::test(EditAdvisorySeverity::class, [
            'record' => $advisorySeverity->getRouteKey(),
        ])
            ->fillForm(['name' => 'Critical'])
            ->call('save'))
            ->toThrow(UniqueConstraintViolationException::class);
    });
});
