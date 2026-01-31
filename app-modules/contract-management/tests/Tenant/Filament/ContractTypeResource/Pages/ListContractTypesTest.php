<?php

use AidingApp\Authorization\Enums\LicenseType;
use AidingApp\ContractManagement\Filament\Resources\ContractResource\Pages\ListContracts;
use AidingApp\ContractManagement\Filament\Resources\ContractTypeResource\Pages\ListContractTypes;
use App\Models\User;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('is gated with proper access control', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->contractManagement = false;
    $settings->save();

    $user = User::factory()->licensed(LicenseType::cases())->create();

    $user->givePermissionTo('settings.view-any');

    actingAs($user);

    get(ListContractTypes::getUrl())->assertForbidden();

    $settings->data->addons->contractManagement = true;
    $settings->save();

    $user->revokePermissionTo('settings.view-any');

    get(ListContractTypes::getUrl())->assertForbidden();

    $user->givePermissionTo('settings.view-any');

    get(ListContractTypes::getUrl())->assertSuccessful();
});