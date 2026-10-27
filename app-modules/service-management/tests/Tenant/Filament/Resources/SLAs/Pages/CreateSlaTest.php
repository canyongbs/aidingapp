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

use AidingApp\ServiceManagement\Filament\Resources\SLAs\Pages\CreateSla;
use AidingApp\ServiceManagement\Filament\Resources\SLAs\SlaResource;
use AidingApp\ServiceManagement\Models\Sla;
use AidingApp\ServiceManagement\Tests\Tenant\RequestFactories\SlaRequestFactory;
use App\Features\SlaNameUniquenessFeature;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\get;
use function Tests\asSuperAdmin;

it('can render the create SLA page', function (): void {
    asSuperAdmin();

    get(SlaResource::getUrl('create'))->assertSuccessful();
});

it('can create an SLA', function (): void {
    asSuperAdmin();

    $request = SlaRequestFactory::new()->create();

    assertDatabaseMissing(Sla::class, ['name' => $request['name']]);

    Livewire::test(CreateSla::class)
        ->fillForm($request)
        ->call('create')
        ->assertHasNoFormErrors();

    assertDatabaseHas(Sla::class, $request);
});

it('allows reusing the name of a soft-deleted SLA', function (): void {
    asSuperAdmin();

    Sla::query()->create(['name' => 'Gold Support'])->delete();

    $request = SlaRequestFactory::new()->state(['name' => 'gold support'])->create();

    Livewire::test(CreateSla::class)
        ->fillForm($request)
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Sla::query()->where('name', 'gold support')->count())->toBe(1)
        ->and(Sla::onlyTrashed()->where('name', 'Gold Support')->exists())->toBeTrue();
});

it('validates the inputs', function (SlaRequestFactory $data, array $errors): void {
    asSuperAdmin();

    Sla::query()->create(['name' => 'Gold Support']);

    $request = SlaRequestFactory::new($data)->create();

    Livewire::test(CreateSla::class)
        ->fillForm($request)
        ->call('create')
        ->assertHasFormErrors($errors);
})->with([
    'name required' => [
        SlaRequestFactory::new()->without('name'),
        ['name' => 'required'],
    ],
    'name max' => [
        SlaRequestFactory::new()->state(['name' => str()->random(256)]),
        ['name' => 'max'],
    ],
    'name unique' => [
        SlaRequestFactory::new()->state(['name' => 'Gold Support']),
        ['name' => 'unique'],
    ],
    'name unique case-insensitively' => [
        SlaRequestFactory::new()->state(['name' => 'GOLD SUPPORT']),
        ['name' => 'unique'],
    ],
]);

describe('name uniqueness feature flag', function (): void {
    it('does not apply the unique form rule when the feature is disabled', function (): void {
        SlaNameUniquenessFeature::deactivate();

        asSuperAdmin();

        Sla::query()->create(['name' => 'Gold Support']);

        $request = SlaRequestFactory::new()->state(['name' => 'Gold Support'])->create();

        expect(fn () => Livewire::test(CreateSla::class)
            ->fillForm($request)
            ->call('create'))
            ->toThrow(UniqueConstraintViolationException::class);
    });
});

describe('authorization', function (): void {
    it('denies access to the create page without the `settings.create` permission', function (): void {
        $user = User::factory()->create();
        $user->givePermissionTo('settings.view-any');
        actingAs($user);

        get(SlaResource::getUrl('create'))->assertForbidden();

        Livewire::test(CreateSla::class)->assertForbidden();
    });

    it('allows access to the create page with the `settings.create` permission', function (): void {
        $user = User::factory()->create();
        $user->givePermissionTo(['settings.view-any', 'settings.create']);
        actingAs($user);

        get(SlaResource::getUrl('create'))->assertSuccessful();

        Livewire::test(CreateSla::class)->assertSuccessful();
    });
});
