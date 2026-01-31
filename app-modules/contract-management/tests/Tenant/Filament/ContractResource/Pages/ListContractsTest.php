<?php

use AidingApp\Authorization\Enums\LicenseType;
use AidingApp\ContractManagement\Filament\Resources\ContractResource\Pages\ListContracts;
use App\Models\User;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('is gated with proper access control', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->contractManagement = false;
    $settings->save();

    $user = User::factory()->licensed(LicenseType::cases())->create();

    $user->givePermissionTo('contract.view-any');

    actingAs($user);

    get(ListContracts::getUrl())->assertForbidden();

    $settings->data->addons->contractManagement = true;
    $settings->save();

    $user->revokePermissionTo('contract.view-any');

    get(ListContracts::getUrl())->assertForbidden();

    $user->givePermissionTo('contract.view-any');

    get(ListContracts::getUrl())->assertSuccessful();
});