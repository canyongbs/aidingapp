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

use AidingApp\InventoryManagement\Filament\Resources\AssetStatuses\AssetStatusResource;
use AidingApp\InventoryManagement\Filament\Resources\AssetStatuses\Pages\CreateAssetStatus;
use AidingApp\InventoryManagement\Models\AssetStatus;
use AidingApp\InventoryManagement\Tests\Tenant\Filament\Resources\AssetStatuses\RequestFactories\CreateAssetStatusRequestFactory;
use App\Features\AssetStatusNameUniquenessFeature;
use Illuminate\Database\UniqueConstraintViolationException;
use Livewire\Livewire;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\get;
use function Tests\asSuperAdmin;

it('can render the create asset status page', function (): void {
    asSuperAdmin();

    get(AssetStatusResource::getUrl('create'))->assertSuccessful();
});

it('can create an asset status', function (): void {
    asSuperAdmin();

    $request = CreateAssetStatusRequestFactory::new()->create();

    assertDatabaseMissing(AssetStatus::class, ['name' => $request['name']]);

    Livewire::test(CreateAssetStatus::class)
        ->fillForm($request)
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertRedirect();

    assertDatabaseHas(AssetStatus::class, $request);
});

it('can create an asset status with a soft deleted asset status name', function (): void {
    asSuperAdmin();

    AssetStatus::factory()->create(['name' => 'Reserved'])->delete();

    $request = CreateAssetStatusRequestFactory::new()->state(['name' => 'Reserved'])->create();

    Livewire::test(CreateAssetStatus::class)
        ->fillForm($request)
        ->call('create')
        ->assertHasNoFormErrors();

    expect(AssetStatus::query()->where('name', 'Reserved')->count())->toBe(1);
});

it('validates the inputs', function (CreateAssetStatusRequestFactory $data, array $errors): void {
    asSuperAdmin();

    AssetStatus::factory()->create(['name' => 'Reserved']);

    Livewire::test(CreateAssetStatus::class)
        ->fillForm($data->create())
        ->call('create')
        ->assertHasFormErrors($errors);
})->with([
    'name required' => [CreateAssetStatusRequestFactory::new()->state(['name' => null]), ['name' => 'required']],
    'name max' => [CreateAssetStatusRequestFactory::new()->state(['name' => str()->random(256)]), ['name' => 'max']],
    'name unique' => [CreateAssetStatusRequestFactory::new()->state(['name' => 'Reserved']), ['name' => 'unique']],
    'name unique case insensitive' => [CreateAssetStatusRequestFactory::new()->state(['name' => 'RESERVED']), ['name' => 'unique']],
    'classification required' => [CreateAssetStatusRequestFactory::new()->state(['classification' => null]), ['classification' => 'required']],
]);

// TODO: Cleanup Task (asset-type-status-and-location-name-uniqueness): delete this describe and the test within
describe('name uniqueness feature flag', function (): void {
    it('does not apply the unique form rule when the feature is disabled', function (): void {
        AssetStatusNameUniquenessFeature::deactivate();

        asSuperAdmin();

        AssetStatus::factory()->create(['name' => 'Reserved']);

        $request = CreateAssetStatusRequestFactory::new()->state(['name' => 'Reserved'])->create();

        expect(fn () => Livewire::test(CreateAssetStatus::class)
            ->fillForm($request)
            ->call('create'))
            ->toThrow(UniqueConstraintViolationException::class);
    });
});
