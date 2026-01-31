<?php

use AidingApp\Authorization\Enums\LicenseType;
use AidingApp\ContractManagement\Filament\Resources\ContractResource\Pages\ListContracts;
use AidingApp\ContractManagement\Filament\Resources\ContractTypeResource\Pages\ListContractTypes;
use AidingApp\LicenseManagement\Filament\Resources\ProductResource\Pages\ListProducts;
use AidingApp\LicenseManagement\Filament\Resources\ProductResource\Pages\ManageProductLicenses;
use AidingApp\LicenseManagement\Models\Product;
use App\Models\User;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('is gated with proper access control', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->licenseManagement = false;
    $settings->save();

    $user = User::factory()->licensed(LicenseType::cases())->create();
    $product = Product::factory()->create();

    $user->givePermissionTo('product.view-any');
    $user->givePermissionTo('product.*.view');
    $user->givePermissionTo('product_license.view-any');

    actingAs($user);

    get(ManageProductLicenses::getUrl(['record' => $product]))->assertForbidden();

    $settings->data->addons->licenseManagement = true;
    $settings->save();

    $user->revokePermissionTo('product.view-any');
    $user->revokePermissionTo('product.*.view');
    $user->revokePermissionTo('product_license.view-any');

    get(ManageProductLicenses::getUrl(['record' => $product]))->assertForbidden();

    $user->givePermissionTo('product.view-any');
    $user->givePermissionTo('product.*.view');
    $user->givePermissionTo('product_license.view-any');

    get(ManageProductLicenses::getUrl(['record' => $product]))->assertSuccessful();
});