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
use AidingApp\InventoryManagement\Filament\Resources\MaintenanceProviders\Pages\EditMaintenanceProvider;
use AidingApp\InventoryManagement\Models\MaintenanceProvider;
use AidingApp\InventoryManagement\Tests\Tenant\Filament\Resources\MaintenanceProviders\RequestFactories\EditMaintenanceProviderRequestFactory;
use App\Features\MaintenanceProviderNameUniquenessFeature;
use App\Models\User;
use App\Settings\LicenseSettings;
use Illuminate\Database\UniqueConstraintViolationException;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\get;
use function Tests\asSuperAdmin;

it('can render the edit maintenance provider page', function (): void {
    asSuperAdmin();
    $maintenanceProvider = MaintenanceProvider::factory()->create();

    get(MaintenanceProviderResource::getUrl('edit', ['record' => $maintenanceProvider]))->assertSuccessful();
});

it('displays the maintenance provider data', function (): void {
    asSuperAdmin();
    $maintenanceProvider = MaintenanceProvider::factory()->create();

    Livewire::test(EditMaintenanceProvider::class, ['record' => $maintenanceProvider->getRouteKey()])
        ->assertSchemaStateSet([
            'name' => $maintenanceProvider->name,
        ]);
});

it('can update a maintenance provider', function (): void {
    $user = User::factory()->create();
    $user->givePermissionTo(['maintenance_provider.view-any', 'maintenance_provider.*.update']);
    actingAs($user);

    $maintenanceProvider = MaintenanceProvider::factory()->create();

    $request = EditMaintenanceProviderRequestFactory::new()->create();

    Livewire::test(EditMaintenanceProvider::class, ['record' => $maintenanceProvider->getRouteKey()])
        ->fillForm($request)
        ->call('save')
        ->assertHasNoFormErrors();

    assertDatabaseHas(MaintenanceProvider::class, [
        'id' => $maintenanceProvider->getKey(),
        ...$request,
    ]);
});

it('allows saving with the same name as the current maintenance provider', function (): void {
    asSuperAdmin();
    $maintenanceProvider = MaintenanceProvider::factory()->create(['name' => 'Acme Repairs']);

    Livewire::test(EditMaintenanceProvider::class, ['record' => $maintenanceProvider->getRouteKey()])
        ->fillForm(['name' => 'ACME REPAIRS'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($maintenanceProvider->refresh()->name)->toBe('ACME REPAIRS');
});

it('allows saving with the name of a soft deleted maintenance provider', function (): void {
    asSuperAdmin();

    MaintenanceProvider::factory()->create(['name' => 'Acme Repairs'])->delete();
    $maintenanceProvider = MaintenanceProvider::factory()->create(['name' => 'Globex Services']);

    Livewire::test(EditMaintenanceProvider::class, ['record' => $maintenanceProvider->getRouteKey()])
        ->fillForm(['name' => 'Acme Repairs'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($maintenanceProvider->refresh()->name)->toBe('Acme Repairs');
});

it('validates the inputs', function (EditMaintenanceProviderRequestFactory $data, array $errors): void {
    asSuperAdmin();

    MaintenanceProvider::factory()->create(['name' => 'Acme Repairs']);
    $maintenanceProvider = MaintenanceProvider::factory()->create();

    Livewire::test(EditMaintenanceProvider::class, ['record' => $maintenanceProvider->getRouteKey()])
        ->fillForm($data->create())
        ->call('save')
        ->assertHasFormErrors($errors);
})->with([
    'name required' => [EditMaintenanceProviderRequestFactory::new()->state(['name' => null]), ['name' => 'required']],
    'name max' => [EditMaintenanceProviderRequestFactory::new()->state(['name' => str()->random(256)]), ['name' => 'max']],
    'name unique' => [EditMaintenanceProviderRequestFactory::new()->state(['name' => 'Acme Repairs']), ['name' => 'unique']],
    'name unique case insensitive' => [EditMaintenanceProviderRequestFactory::new()->state(['name' => 'ACME REPAIRS']), ['name' => 'unique']],
]);

describe('name uniqueness feature flag', function (): void {
    it('does not apply the unique form rule when the feature is disabled', function (): void {
        MaintenanceProviderNameUniquenessFeature::deactivate();

        asSuperAdmin();

        MaintenanceProvider::factory()->create(['name' => 'Acme Repairs']);
        $maintenanceProvider = MaintenanceProvider::factory()->create(['name' => 'Globex Services']);

        expect(fn () => Livewire::test(EditMaintenanceProvider::class, ['record' => $maintenanceProvider->getRouteKey()])
            ->fillForm(['name' => 'Acme Repairs'])
            ->call('save'))
            ->toThrow(UniqueConstraintViolationException::class);
    });
});

describe('authorization', function (): void {
    it('denies access to the edit page without the `maintenance_provider.*.update` permission', function (): void {
        $user = User::factory()->create();
        $user->givePermissionTo('maintenance_provider.view-any');
        actingAs($user);

        $maintenanceProvider = MaintenanceProvider::factory()->create();

        get(MaintenanceProviderResource::getUrl('edit', ['record' => $maintenanceProvider]))->assertForbidden();

        Livewire::test(EditMaintenanceProvider::class, ['record' => $maintenanceProvider->getRouteKey()])->assertForbidden();
    });

    it('denies access to the edit page when the asset management addon is disabled', function (): void {
        $settings = app(LicenseSettings::class);
        $settings->data->addons->assetManagement = false;
        $settings->save();

        $user = User::factory()->create();
        $user->givePermissionTo(['maintenance_provider.view-any', 'maintenance_provider.*.update']);
        actingAs($user);

        $maintenanceProvider = MaintenanceProvider::factory()->create();

        get(MaintenanceProviderResource::getUrl('edit', ['record' => $maintenanceProvider]))->assertForbidden();
    });

    it('allows access to the edit page with the `maintenance_provider.*.update` permission', function (): void {
        $user = User::factory()->create();
        $user->givePermissionTo(['maintenance_provider.view-any', 'maintenance_provider.*.update']);
        actingAs($user);

        $maintenanceProvider = MaintenanceProvider::factory()->create();

        get(MaintenanceProviderResource::getUrl('edit', ['record' => $maintenanceProvider]))->assertSuccessful();

        Livewire::test(EditMaintenanceProvider::class, ['record' => $maintenanceProvider->getRouteKey()])->assertSuccessful();
    });
});
