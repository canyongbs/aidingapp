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

use AidingApp\Authorization\Models\Permission;
use AidingApp\Authorization\Models\PermissionGroup;
use AidingApp\Contact\Models\Contact;
use AidingApp\LicenseManagement\Filament\Resources\Products\Pages\ManageProductLicenses;
use AidingApp\LicenseManagement\Models\Product;
use AidingApp\LicenseManagement\Models\ProductLicense;
use App\Features\ContactArchivingFeature;
use App\Features\LicensesPermissionsConsolidationFeature;
use App\Models\User;
use App\Settings\LicenseSettings;
use Filament\Actions\Testing\TestAction;
use Filament\Forms\Components\Select;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Tests\asSuperAdmin;

it('is gated with proper access control', function (): void {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->licenseManagement = false;
    $settings->save();

    $user = User::factory()->create();
    $product = Product::factory()->create();

    $user->givePermissionTo(['licenses.view-any', 'licenses.*.view']);

    actingAs($user);

    get(ManageProductLicenses::getUrl(['record' => $product]))->assertForbidden();

    $settings->data->addons->licenseManagement = true;
    $settings->save();

    $user->revokePermissionTo(['licenses.view-any', 'licenses.*.view']);

    get(ManageProductLicenses::getUrl(['record' => $product]))->assertForbidden();

    $user->givePermissionTo(['licenses.view-any', 'licenses.*.view']);

    get(ManageProductLicenses::getUrl(['record' => $product]))->assertSuccessful();
});

test('ManageProductLicenses does not offer archived contacts as the license assignee', function (): void {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->licenseManagement = true;
    $settings->save();

    asSuperAdmin();

    $activeContact = Contact::factory()->create();
    $archivedContact = Contact::factory()->archived()->create();

    $product = Product::factory()->create();

    Livewire::test(ManageProductLicenses::class, ['record' => $product->getRouteKey()])
        ->mountTableAction('create')
        ->assertFormFieldExists('assigned_to', 'mountedActionSchema0', function (Select $field) use ($activeContact, $archivedContact): bool {
            $options = $field->getOptions();

            return array_key_exists($activeContact->getKey(), $options)
                && ! array_key_exists($archivedContact->getKey(), $options);
        });
});

test('ManageProductLicenses keeps an archived contact that is already assigned to the license', function (): void {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->licenseManagement = true;
    $settings->save();

    asSuperAdmin();

    $archivedContact = Contact::factory()->archived()->create();

    $productLicense = ProductLicense::factory()->for($archivedContact, 'contact')->create();

    Livewire::test(ManageProductLicenses::class, ['record' => $productLicense->product->getRouteKey()])
        ->mountTableAction('edit', $productLicense)
        ->assertFormFieldExists('assigned_to', 'mountedActionSchema0', function (Select $field) use ($archivedContact): bool {
            return array_key_exists($archivedContact->getKey(), $field->getOptions());
        })
        ->callMountedTableAction()
        ->assertHasNoFormErrors();

    expect($productLicense->refresh()->assigned_to)->toBe($archivedContact->getKey());
});

test('ManageProductLicenses offers every contact as the license assignee when the contact archiving feature is inactive', function (): void {
    ContactArchivingFeature::deactivate();

    $settings = app(LicenseSettings::class);
    $settings->data->addons->licenseManagement = true;
    $settings->save();

    asSuperAdmin();

    $archivedContact = Contact::factory()->archived()->create();

    $product = Product::factory()->create();

    Livewire::test(ManageProductLicenses::class, ['record' => $product->getRouteKey()])
        ->mountTableAction('create')
        ->assertFormFieldExists('assigned_to', 'mountedActionSchema0', function (Select $field) use ($archivedContact): bool {
            return array_key_exists($archivedContact->getKey(), $field->getOptions());
        });
});

// TODO: Cleanup Task (licenses-permissions-consolidation): delete this test
it('grants access with the legacy product and product license permissions while the consolidation flag is inactive', function (): void {
    LicensesPermissionsConsolidationFeature::deactivate();

    $settings = app(LicenseSettings::class);
    $settings->data->addons->licenseManagement = true;
    $settings->save();

    $legacyPermissions = [
        'product.view-any' => 'Product',
        'product.*.view' => 'Product',
        'product_license.view-any' => 'Product License',
    ];

    foreach ($legacyPermissions as $name => $groupName) {
        Permission::factory()->create([
            'name' => $name,
            'guard_name' => 'web',
            'group_id' => PermissionGroup::firstOrCreate(['name' => $groupName])->getKey(),
        ]);
    }

    $user = User::factory()->create();
    $product = Product::factory()->create();

    actingAs($user);

    get(ManageProductLicenses::getUrl(['record' => $product]))->assertForbidden();

    $user->givePermissionTo(array_keys($legacyPermissions));

    get(ManageProductLicenses::getUrl(['record' => $product]))->assertSuccessful();
});

describe('action authorization', function (): void {
    beforeEach(function (): void {
        $settings = app(LicenseSettings::class);
        $settings->data->addons->licenseManagement = true;
        $settings->save();
    });

    $testAction = fn (string $action, ProductLicense $productLicense): TestAction => $action === 'create'
        ? TestAction::make($action)->table()
        : TestAction::make($action)->table($productLicense);

    it('only shows each license action to a user with the consolidated permission', function (string $action, string $permission) use ($testAction): void {
        $user = User::factory()->create();
        $user->givePermissionTo(['licenses.view-any', 'licenses.*.view']);

        // Non-admin users only see the licenses they created
        $productLicense = ProductLicense::factory()->create(['created_by_id' => $user->getKey()]);

        actingAs($user);

        Livewire::test(ManageProductLicenses::class, ['record' => $productLicense->product->getRouteKey()])
            ->assertActionHidden($testAction($action, $productLicense));

        $user->givePermissionTo($permission);

        Livewire::test(ManageProductLicenses::class, ['record' => $productLicense->product->getRouteKey()])
            ->assertActionVisible($testAction($action, $productLicense));
    })->with([
        'create' => ['create', 'licenses.create'],
        'edit' => ['edit', 'licenses.*.update'],
        'delete' => ['delete', 'licenses.*.delete'],
    ]);

    // TODO: Cleanup Task (licenses-permissions-consolidation): delete this test
    it('only shows each license action to a user with the legacy permission while the consolidation flag is inactive', function (string $action, string $permission) use ($testAction): void {
        LicensesPermissionsConsolidationFeature::deactivate();

        $legacyPermissions = [
            'product.view-any' => 'Product',
            'product.*.view' => 'Product',
            'product_license.view-any' => 'Product License',
            $permission => 'Product License',
        ];

        foreach ($legacyPermissions as $name => $groupName) {
            Permission::factory()->create([
                'name' => $name,
                'guard_name' => 'web',
                'group_id' => PermissionGroup::firstOrCreate(['name' => $groupName])->getKey(),
            ]);
        }

        $user = User::factory()->create();
        $user->givePermissionTo(['product.view-any', 'product.*.view', 'product_license.view-any']);

        $productLicense = ProductLicense::factory()->create(['created_by_id' => $user->getKey()]);

        actingAs($user);

        Livewire::test(ManageProductLicenses::class, ['record' => $productLicense->product->getRouteKey()])
            ->assertActionHidden($testAction($action, $productLicense));

        $user->givePermissionTo($permission);

        Livewire::test(ManageProductLicenses::class, ['record' => $productLicense->product->getRouteKey()])
            ->assertActionVisible($testAction($action, $productLicense));
    })->with([
        'create' => ['create', 'product_license.create'],
        'edit' => ['edit', 'product_license.*.update'],
        'delete' => ['delete', 'product_license.*.delete'],
    ]);
});
