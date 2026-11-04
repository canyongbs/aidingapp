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

use AidingApp\ContractManagement\Filament\Resources\Contracts\ContractResource;
use AidingApp\ContractManagement\Filament\Resources\Contracts\Pages\EditContract;
use AidingApp\ContractManagement\Models\Contract;
use AidingApp\ContractManagement\Tests\Tenant\Filament\Resources\Contracts\RequestFactories\EditContractRequestFactory;
use App\Features\ContractNameUniquenessFeature;
use App\Models\User;
use App\Settings\LicenseSettings;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Tests\asSuperAdmin;

it('can render the edit contract page', function (): void {
    asSuperAdmin();
    $contract = Contract::factory()->create();

    get(ContractResource::getUrl('edit', ['record' => $contract]))->assertSuccessful();
});

it('can update a contract', function (): void {
    asSuperAdmin();
    $contract = Contract::factory()->create();

    $request = EditContractRequestFactory::new()->create();

    Livewire::test(EditContract::class, ['record' => $contract->getRouteKey()])
        ->fillForm($request)
        ->call('save')
        ->assertHasNoFormErrors();

    expect($contract->refresh()->name)->toBe($request['name']);
});

it('allows saving with the same name as the current contract in a different case', function (): void {
    asSuperAdmin();
    $contract = Contract::factory()->create(['name' => 'Master Agreement']);

    Livewire::test(EditContract::class, ['record' => $contract->getRouteKey()])
        ->fillForm(['name' => 'MASTER AGREEMENT'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($contract->refresh()->name)->toBe('MASTER AGREEMENT');
});

it('allows saving with the name of a soft deleted contract', function (): void {
    asSuperAdmin();

    Contract::factory()->create(['name' => 'Master Agreement'])->delete();
    $contract = Contract::factory()->create(['name' => 'Service Agreement']);

    Livewire::test(EditContract::class, ['record' => $contract->getRouteKey()])
        ->fillForm(['name' => 'Master Agreement'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($contract->refresh()->name)->toBe('Master Agreement');
});

it('validates the inputs', function (EditContractRequestFactory $data, array $errors): void {
    asSuperAdmin();

    Contract::factory()->create(['name' => 'Master Agreement']);
    $contract = Contract::factory()->create();

    Livewire::test(EditContract::class, ['record' => $contract->getRouteKey()])
        ->fillForm($data->create())
        ->call('save')
        ->assertHasFormErrors($errors);
})->with([
    'name required' => [EditContractRequestFactory::new()->state(['name' => null]), ['name' => 'required']],
    'name max' => [EditContractRequestFactory::new()->state(['name' => str()->random(256)]), ['name' => 'max']],
    'name unique' => [EditContractRequestFactory::new()->state(['name' => 'Master Agreement']), ['name' => 'unique']],
    'name unique case insensitive' => [EditContractRequestFactory::new()->state(['name' => 'MASTER AGREEMENT']), ['name' => 'unique']],
]);

// TODO: Cleanup Task (contract-name-uniqueness): delete this describe and the test within
describe('name uniqueness feature flag', function (): void {
    it('does not apply the unique form rule when the feature is disabled', function (): void {
        ContractNameUniquenessFeature::deactivate();
        DB::statement('DROP INDEX IF EXISTS contracts_name_unique');

        asSuperAdmin();

        Contract::factory()->create(['name' => 'Master Agreement']);
        $contract = Contract::factory()->create(['name' => 'Service Agreement']);

        Livewire::test(EditContract::class, ['record' => $contract->getRouteKey()])
            ->fillForm(['name' => 'Master Agreement'])
            ->call('save')
            ->assertHasNoFormErrors();

        expect($contract->refresh()->name)->toBe('Master Agreement');
    });
});

describe('authorization', function (): void {
    it('denies access to the edit page without the `contract.*.update` permission', function (): void {
        $user = User::factory()->create();
        $user->givePermissionTo('contract.view-any');
        actingAs($user);

        $contract = Contract::factory()->create();

        get(ContractResource::getUrl('edit', ['record' => $contract]))->assertForbidden();

        Livewire::test(EditContract::class, ['record' => $contract->getRouteKey()])->assertForbidden();
    });

    it('denies access to the edit page when the contract management addon is disabled', function (): void {
        $settings = app(LicenseSettings::class);
        $settings->data->addons->contractManagement = false;
        $settings->save();

        $user = User::factory()->create();
        $user->givePermissionTo(['contract.view-any', 'contract.*.update']);
        actingAs($user);

        $contract = Contract::factory()->create();

        get(ContractResource::getUrl('edit', ['record' => $contract]))->assertForbidden();
    });

    it('allows access to the edit page with the `contract.*.update` permission', function (): void {
        $user = User::factory()->create();
        $user->givePermissionTo(['contract.view-any', 'contract.*.update']);
        actingAs($user);

        $contract = Contract::factory()->create();

        get(ContractResource::getUrl('edit', ['record' => $contract]))->assertSuccessful();

        Livewire::test(EditContract::class, ['record' => $contract->getRouteKey()])->assertSuccessful();
    });
});
