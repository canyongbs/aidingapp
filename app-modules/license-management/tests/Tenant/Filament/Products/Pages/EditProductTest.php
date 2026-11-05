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

use AidingApp\LicenseManagement\Filament\Resources\Products\Pages\EditProduct;
use AidingApp\LicenseManagement\Filament\Resources\Products\ProductResource;
use AidingApp\LicenseManagement\Models\Product;
use AidingApp\LicenseManagement\Tests\Tenant\Filament\Products\RequestFactories\EditProductRequestFactory;
use App\Features\ProductNameUniquenessFeature;
use App\Models\User;
use App\Settings\LicenseSettings;
use Illuminate\Database\UniqueConstraintViolationException;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\get;
use function Tests\asSuperAdmin;

it('can render the edit product page', function (): void {
    asSuperAdmin();
    $product = Product::factory()->create();

    get(ProductResource::getUrl('edit', ['record' => $product]))->assertSuccessful();
});

it('displays the product data', function (): void {
    asSuperAdmin();
    $product = Product::factory()->create();

    Livewire::test(EditProduct::class, ['record' => $product->getRouteKey()])
        ->assertSchemaStateSet([
            'name' => $product->name,
            'url' => $product->url,
            'description' => $product->description,
            'version' => $product->version,
            'additional_notes' => $product->additional_notes,
        ]);
});

it('can update a product', function (): void {
    $user = User::factory()->create();
    $user->givePermissionTo(['product.view-any', 'product.*.update']);
    actingAs($user);

    $product = Product::factory()->create();

    $request = EditProductRequestFactory::new()->create();

    Livewire::test(EditProduct::class, ['record' => $product->getRouteKey()])
        ->fillForm($request)
        ->call('save')
        ->assertHasNoFormErrors();

    assertDatabaseHas(Product::class, [
        'id' => $product->getKey(),
        ...$request,
    ]);
});

it('allows saving with the same name as the current product', function (): void {
    asSuperAdmin();
    $product = Product::factory()->create(['name' => 'Aiding App']);

    Livewire::test(EditProduct::class, ['record' => $product->getRouteKey()])
        ->fillForm(['name' => 'AIDING APP'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($product->refresh()->name)->toBe('AIDING APP');
});

it('allows saving with the name of a soft deleted product', function (): void {
    asSuperAdmin();

    Product::factory()->create(['name' => 'Aiding App'])->delete();
    $product = Product::factory()->create(['name' => 'Advising App']);

    Livewire::test(EditProduct::class, ['record' => $product->getRouteKey()])
        ->fillForm(['name' => 'Aiding App'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($product->refresh()->name)->toBe('Aiding App');
});

it('validates the inputs', function (EditProductRequestFactory $data, array $errors): void {
    asSuperAdmin();

    Product::factory()->create(['name' => 'Aiding App']);
    $product = Product::factory()->create();

    Livewire::test(EditProduct::class, ['record' => $product->getRouteKey()])
        ->fillForm($data->create())
        ->call('save')
        ->assertHasFormErrors($errors);
})->with([
    'name required' => [EditProductRequestFactory::new()->state(['name' => null]), ['name' => 'required']],
    'name max' => [EditProductRequestFactory::new()->state(['name' => str()->random(256)]), ['name' => 'max']],
    'name unique' => [EditProductRequestFactory::new()->state(['name' => 'Aiding App']), ['name' => 'unique']],
    'name unique case insensitive' => [EditProductRequestFactory::new()->state(['name' => 'AIDING APP']), ['name' => 'unique']],
    'url url' => [EditProductRequestFactory::new()->state(['url' => 'not-a-url']), ['url' => 'url']],
]);

describe('name uniqueness feature flag', function (): void {
    it('does not apply the unique form rule when the feature is disabled', function (): void {
        ProductNameUniquenessFeature::deactivate();

        asSuperAdmin();

        Product::factory()->create(['name' => 'Aiding App']);
        $product = Product::factory()->create(['name' => 'Advising App']);

        expect(fn () => Livewire::test(EditProduct::class, ['record' => $product->getRouteKey()])
            ->fillForm(['name' => 'Aiding App'])
            ->call('save'))
            ->toThrow(UniqueConstraintViolationException::class);
    });
});

describe('authorization', function (): void {
    it('denies access to the edit page without the `product.*.update` permission', function (): void {
        $user = User::factory()->create();
        $user->givePermissionTo('product.view-any');
        actingAs($user);

        $product = Product::factory()->create();

        get(ProductResource::getUrl('edit', ['record' => $product]))->assertForbidden();

        Livewire::test(EditProduct::class, ['record' => $product->getRouteKey()])->assertForbidden();
    });

    it('denies access to the edit page when the license management addon is disabled', function (): void {
        $settings = app(LicenseSettings::class);
        $settings->data->addons->licenseManagement = false;
        $settings->save();

        $user = User::factory()->create();
        $user->givePermissionTo(['product.view-any', 'product.*.update']);
        actingAs($user);

        $product = Product::factory()->create();

        get(ProductResource::getUrl('edit', ['record' => $product]))->assertForbidden();
    });

    it('allows access to the edit page with the `product.*.update` permission', function (): void {
        $user = User::factory()->create();
        $user->givePermissionTo(['product.view-any', 'product.*.update']);
        actingAs($user);

        $product = Product::factory()->create();

        get(ProductResource::getUrl('edit', ['record' => $product]))->assertSuccessful();

        Livewire::test(EditProduct::class, ['record' => $product->getRouteKey()])->assertSuccessful();
    });
});
