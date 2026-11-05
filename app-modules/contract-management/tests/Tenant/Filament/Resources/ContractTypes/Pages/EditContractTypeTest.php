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

use AidingApp\ContractManagement\Filament\Resources\ContractTypes\ContractTypeResource;
use AidingApp\ContractManagement\Filament\Resources\ContractTypes\Pages\EditContractType;
use AidingApp\ContractManagement\Models\ContractType;
use AidingApp\ContractManagement\Tests\Tenant\Filament\Resources\ContractTypes\RequestFactories\EditContractTypeRequestFactory;
use App\Features\ContractTypeNameUniquenessFeature;
use App\Models\User;
use App\Settings\LicenseSettings;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Tests\asSuperAdmin;

it('can render the edit contract type page', function (): void {
    asSuperAdmin();
    $contractType = ContractType::factory()->create();

    get(ContractTypeResource::getUrl('edit', ['record' => $contractType]))->assertSuccessful();
});

it('can update a contract type', function (): void {
    asSuperAdmin();
    $contractType = ContractType::factory()->create();

    $request = EditContractTypeRequestFactory::new()->create();

    Livewire::test(EditContractType::class, ['record' => $contractType->getRouteKey()])
        ->fillForm($request)
        ->call('save')
        ->assertHasNoFormErrors();

    expect($contractType->refresh()->name)->toBe($request['name']);
});

it('allows saving with the same name as the current contract type in a different case', function (): void {
    asSuperAdmin();
    $contractType = ContractType::factory()->create(['name' => 'Software License']);

    Livewire::test(EditContractType::class, ['record' => $contractType->getRouteKey()])
        ->fillForm(['name' => 'SOFTWARE LICENSE'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($contractType->refresh()->name)->toBe('SOFTWARE LICENSE');
});

it('allows saving with the name of a soft deleted contract type', function (): void {
    asSuperAdmin();

    ContractType::factory()->create(['name' => 'Software License'])->delete();
    $contractType = ContractType::factory()->create(['name' => 'Hardware Lease']);

    Livewire::test(EditContractType::class, ['record' => $contractType->getRouteKey()])
        ->fillForm(['name' => 'Software License'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($contractType->refresh()->name)->toBe('Software License');
});

it('validates the inputs', function (EditContractTypeRequestFactory $data, array $errors): void {
    asSuperAdmin();

    ContractType::factory()->create(['name' => 'Software License']);
    $contractType = ContractType::factory()->create();

    Livewire::test(EditContractType::class, ['record' => $contractType->getRouteKey()])
        ->fillForm($data->create())
        ->call('save')
        ->assertHasFormErrors($errors);
})->with([
    'name required' => [EditContractTypeRequestFactory::new()->state(['name' => null]), ['name' => 'required']],
    'name max' => [EditContractTypeRequestFactory::new()->state(['name' => str()->random(256)]), ['name' => 'max']],
    'name unique' => [EditContractTypeRequestFactory::new()->state(['name' => 'Software License']), ['name' => 'unique']],
    'name unique case insensitive' => [EditContractTypeRequestFactory::new()->state(['name' => 'SOFTWARE LICENSE']), ['name' => 'unique']],
]);

// TODO: Cleanup Task (contract-name-uniqueness): delete this describe and the test within
describe('name uniqueness feature flag', function (): void {
    it('does not apply the unique form rule when the feature is disabled', function (): void {
        ContractTypeNameUniquenessFeature::deactivate();
        DB::statement('DROP INDEX IF EXISTS contract_types_name_unique');

        asSuperAdmin();

        ContractType::factory()->create(['name' => 'Software License']);
        $contractType = ContractType::factory()->create(['name' => 'Hardware Lease']);

        Livewire::test(EditContractType::class, ['record' => $contractType->getRouteKey()])
            ->fillForm(['name' => 'Software License'])
            ->call('save')
            ->assertHasNoFormErrors();

        expect($contractType->refresh()->name)->toBe('Software License');
    });
});

describe('authorization', function (): void {
    it('denies access to the edit page without the `settings.*.update` permission', function (): void {
        $user = User::factory()->create();
        $user->givePermissionTo('settings.view-any');
        actingAs($user);

        $contractType = ContractType::factory()->create();

        get(ContractTypeResource::getUrl('edit', ['record' => $contractType]))->assertForbidden();

        Livewire::test(EditContractType::class, ['record' => $contractType->getRouteKey()])->assertForbidden();
    });

    it('denies access to the edit page when the contract management addon is disabled', function (): void {
        $settings = app(LicenseSettings::class);
        $settings->data->addons->contractManagement = false;
        $settings->save();

        $user = User::factory()->create();
        $user->givePermissionTo(['settings.view-any', 'settings.*.update']);
        actingAs($user);

        $contractType = ContractType::factory()->create();

        get(ContractTypeResource::getUrl('edit', ['record' => $contractType]))->assertForbidden();
    });

    it('allows access to the edit page with the `settings.*.update` permission', function (): void {
        $user = User::factory()->create();
        $user->givePermissionTo(['settings.view-any', 'settings.*.update']);
        actingAs($user);

        $contractType = ContractType::factory()->create();

        get(ContractTypeResource::getUrl('edit', ['record' => $contractType]))->assertSuccessful();

        Livewire::test(EditContractType::class, ['record' => $contractType->getRouteKey()])->assertSuccessful();
    });
});
