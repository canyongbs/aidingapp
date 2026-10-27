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

use AidingApp\LicenseManagement\Filament\Resources\Products\Pages\CreateProduct;
use AidingApp\LicenseManagement\Filament\Resources\Products\ProductResource;
use AidingApp\LicenseManagement\Models\Product;
use AidingApp\LicenseManagement\Tests\Tenant\Filament\Products\RequestFactories\CreateProductRequestFactory;
use App\Features\ProductNameUniquenessFeature;
use App\Models\User;
use App\Settings\LicenseSettings;
use Illuminate\Database\UniqueConstraintViolationException;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\get;
use function Tests\asSuperAdmin;

it('can render the create product page', function (): void {
    asSuperAdmin();

    get(ProductResource::getUrl('create'))->assertSuccessful();
});

it('can create a product', function (): void {
    $user = User::factory()->create();
    $user->givePermissionTo(['product.view-any', 'product.create']);
    actingAs($user);

    $request = CreateProductRequestFactory::new()->create();

    assertDatabaseMissing(Product::class, ['name' => $request['name']]);

    Livewire::test(CreateProduct::class)
        ->fillForm($request)
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertRedirect();

    assertDatabaseHas(Product::class, [
        ...$request,
        'created_by_id' => $user->getKey(),
    ]);
});

it('can create a product with a soft deleted product name', function (): void {
    asSuperAdmin();

    Product::factory()->create(['name' => 'Aiding App'])->delete();

    $request = CreateProductRequestFactory::new()->state(['name' => 'Aiding App'])->create();

    Livewire::test(CreateProduct::class)
        ->fillForm($request)
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Product::query()->where('name', 'Aiding App')->count())->toBe(1);
});

it('validates the inputs', function (CreateProductRequestFactory $data, array $errors): void {
    asSuperAdmin();

    Product::factory()->create(['name' => 'Aiding App']);

    Livewire::test(CreateProduct::class)
        ->fillForm($data->create())
        ->call('create')
        ->assertHasFormErrors($errors);
})->with([
    'name required' => [CreateProductRequestFactory::new()->state(['name' => null]), ['name' => 'required']],
    'name max' => [CreateProductRequestFactory::new()->state(['name' => str()->random(256)]), ['name' => 'max']],
    'name unique' => [CreateProductRequestFactory::new()->state(['name' => 'Aiding App']), ['name' => 'unique']],
    'name unique case insensitive' => [CreateProductRequestFactory::new()->state(['name' => 'AIDING APP']), ['name' => 'unique']],
    'url url' => [CreateProductRequestFactory::new()->state(['url' => 'not-a-url']), ['url' => 'url']],
]);

describe('name uniqueness feature flag', function (): void {
    it('does not apply the unique form rule when the feature is disabled', function (): void {
        ProductNameUniquenessFeature::deactivate();

        asSuperAdmin();

        Product::factory()->create(['name' => 'Aiding App']);

        $request = CreateProductRequestFactory::new()->state(['name' => 'Aiding App'])->create();

        expect(fn () => Livewire::test(CreateProduct::class)
            ->fillForm($request)
            ->call('create'))
            ->toThrow(UniqueConstraintViolationException::class);
    });
});

describe('authorization', function (): void {
    it('denies access to the create page without the `product.create` permission', function (): void {
        $user = User::factory()->create();
        $user->givePermissionTo('product.view-any');
        actingAs($user);

        get(ProductResource::getUrl('create'))->assertForbidden();

        Livewire::test(CreateProduct::class)->assertForbidden();
    });

    it('denies access to the create page when the license management addon is disabled', function (): void {
        $settings = app(LicenseSettings::class);
        $settings->data->addons->licenseManagement = false;
        $settings->save();

        $user = User::factory()->create();
        $user->givePermissionTo(['product.view-any', 'product.create']);
        actingAs($user);

        get(ProductResource::getUrl('create'))->assertForbidden();
    });

    it('allows access to the create page with the `product.create` permission', function (): void {
        $user = User::factory()->create();
        $user->givePermissionTo(['product.view-any', 'product.create']);
        actingAs($user);

        get(ProductResource::getUrl('create'))->assertSuccessful();

        Livewire::test(CreateProduct::class)->assertSuccessful();
    });
});
