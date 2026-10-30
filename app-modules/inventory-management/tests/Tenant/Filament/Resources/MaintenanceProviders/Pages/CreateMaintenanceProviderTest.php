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

use AidingApp\InventoryManagement\Filament\Resources\MaintenanceProviders\MaintenanceProviderResource;
use AidingApp\InventoryManagement\Filament\Resources\MaintenanceProviders\Pages\CreateMaintenanceProvider;
use AidingApp\InventoryManagement\Models\MaintenanceProvider;
use AidingApp\InventoryManagement\Tests\Tenant\Filament\Resources\MaintenanceProviders\RequestFactories\CreateMaintenanceProviderRequestFactory;
use App\Features\MaintenanceProviderNameUniquenessFeature;
use App\Models\User;
use App\Settings\LicenseSettings;
use Illuminate\Database\UniqueConstraintViolationException;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\get;
use function Tests\asSuperAdmin;

it('can render the create maintenance provider page', function (): void {
    asSuperAdmin();

    get(MaintenanceProviderResource::getUrl('create'))->assertSuccessful();
});

it('can create a maintenance provider', function (): void {
    $user = User::factory()->create();
    $user->givePermissionTo(['maintenance_provider.view-any', 'maintenance_provider.create']);
    actingAs($user);

    $request = CreateMaintenanceProviderRequestFactory::new()->create();

    assertDatabaseMissing(MaintenanceProvider::class, ['name' => $request['name']]);

    Livewire::test(CreateMaintenanceProvider::class)
        ->fillForm($request)
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertRedirect();

    assertDatabaseHas(MaintenanceProvider::class, $request);
});

it('can create a maintenance provider with a soft deleted maintenance provider name', function (): void {
    asSuperAdmin();

    MaintenanceProvider::factory()->create(['name' => 'Acme Repairs'])->delete();

    $request = CreateMaintenanceProviderRequestFactory::new()->state(['name' => 'Acme Repairs'])->create();

    Livewire::test(CreateMaintenanceProvider::class)
        ->fillForm($request)
        ->call('create')
        ->assertHasNoFormErrors();

    expect(MaintenanceProvider::query()->where('name', 'Acme Repairs')->count())->toBe(1);
});

it('validates the inputs', function (CreateMaintenanceProviderRequestFactory $data, array $errors): void {
    asSuperAdmin();

    MaintenanceProvider::factory()->create(['name' => 'Acme Repairs']);

    Livewire::test(CreateMaintenanceProvider::class)
        ->fillForm($data->create())
        ->call('create')
        ->assertHasFormErrors($errors);
})->with([
    'name required' => [CreateMaintenanceProviderRequestFactory::new()->state(['name' => null]), ['name' => 'required']],
    'name max' => [CreateMaintenanceProviderRequestFactory::new()->state(['name' => str()->random(256)]), ['name' => 'max']],
    'name unique' => [CreateMaintenanceProviderRequestFactory::new()->state(['name' => 'Acme Repairs']), ['name' => 'unique']],
    'name unique case insensitive' => [CreateMaintenanceProviderRequestFactory::new()->state(['name' => 'ACME REPAIRS']), ['name' => 'unique']],
]);

describe('name uniqueness feature flag', function (): void {
    it('does not apply the unique form rule when the feature is disabled', function (): void {
        MaintenanceProviderNameUniquenessFeature::deactivate();

        asSuperAdmin();

        MaintenanceProvider::factory()->create(['name' => 'Acme Repairs']);

        $request = CreateMaintenanceProviderRequestFactory::new()->state(['name' => 'Acme Repairs'])->create();

        expect(fn () => Livewire::test(CreateMaintenanceProvider::class)
            ->fillForm($request)
            ->call('create'))
            ->toThrow(UniqueConstraintViolationException::class);
    });
});

describe('authorization', function (): void {
    it('denies access to the create page without the `maintenance_provider.create` permission', function (): void {
        $user = User::factory()->create();
        $user->givePermissionTo('maintenance_provider.view-any');
        actingAs($user);

        get(MaintenanceProviderResource::getUrl('create'))->assertForbidden();

        Livewire::test(CreateMaintenanceProvider::class)->assertForbidden();
    });

    it('denies access to the create page when the asset management addon is disabled', function (): void {
        $settings = app(LicenseSettings::class);
        $settings->data->addons->assetManagement = false;
        $settings->save();

        $user = User::factory()->create();
        $user->givePermissionTo(['maintenance_provider.view-any', 'maintenance_provider.create']);
        actingAs($user);

        get(MaintenanceProviderResource::getUrl('create'))->assertForbidden();
    });

    it('allows access to the create page with the `maintenance_provider.create` permission', function (): void {
        $user = User::factory()->create();
        $user->givePermissionTo(['maintenance_provider.view-any', 'maintenance_provider.create']);
        actingAs($user);

        get(MaintenanceProviderResource::getUrl('create'))->assertSuccessful();

        Livewire::test(CreateMaintenanceProvider::class)->assertSuccessful();
    });
});
