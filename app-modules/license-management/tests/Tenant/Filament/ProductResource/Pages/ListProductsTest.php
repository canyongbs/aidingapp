<?php

use AidingApp\Authorization\Enums\LicenseType;
use AidingApp\ContractManagement\Filament\Resources\ContractResource\Pages\ListContracts;
use AidingApp\ContractManagement\Filament\Resources\ContractTypeResource\Pages\ListContractTypes;
use AidingApp\LicenseManagement\Filament\Resources\ProductResource\Pages\ListProducts;
use App\Models\User;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('is gated with proper access control', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->licenseManagement = false;
    $settings->save();

    $user = User::factory()->licensed(LicenseType::cases())->create();

    $user->givePermissionTo('product.view-any');

    actingAs($user);

    get(ListProducts::getUrl())->assertForbidden();

    $settings->data->addons->licenseManagement = true;
    $settings->save();

    $user->revokePermissionTo('product.view-any');

    get(ListProducts::getUrl())->assertForbidden();

    $user->givePermissionTo('product.view-any');

    get(ListProducts::getUrl())->assertSuccessful();
});