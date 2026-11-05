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
use AidingApp\ContractManagement\Filament\Resources\Contracts\Pages\CreateContract;
use AidingApp\ContractManagement\Models\Contract;
use AidingApp\ContractManagement\Tests\Tenant\Filament\Resources\Contracts\RequestFactories\CreateContractRequestFactory;
use App\Features\ContractNameUniquenessFeature;
use App\Models\User;
use App\Settings\LicenseSettings;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Tests\asSuperAdmin;

it('can render the create contract page', function (): void {
    asSuperAdmin();

    get(ContractResource::getUrl('create'))->assertSuccessful();
});

it('can create a contract', function (): void {
    asSuperAdmin();

    $request = CreateContractRequestFactory::new()->create();

    Livewire::test(CreateContract::class)
        ->fillForm($request)
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Contract::query()->where('name', $request['name'])->exists())->toBeTrue();
});

it('can create a contract with a soft deleted contract name', function (): void {
    asSuperAdmin();

    Contract::factory()->create(['name' => 'Master Agreement'])->delete();

    $request = CreateContractRequestFactory::new()->state(['name' => 'Master Agreement'])->create();

    Livewire::test(CreateContract::class)
        ->fillForm($request)
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Contract::query()->where('name', 'Master Agreement')->count())->toBe(1);
});

it('validates the inputs', function (CreateContractRequestFactory $data, array $errors): void {
    asSuperAdmin();

    Contract::factory()->create(['name' => 'Master Agreement']);

    Livewire::test(CreateContract::class)
        ->fillForm($data->create())
        ->call('create')
        ->assertHasFormErrors($errors);
})->with([
    'name required' => [CreateContractRequestFactory::new()->state(['name' => null]), ['name' => 'required']],
    'name max' => [CreateContractRequestFactory::new()->state(['name' => str()->random(256)]), ['name' => 'max']],
    'name unique' => [CreateContractRequestFactory::new()->state(['name' => 'Master Agreement']), ['name' => 'unique']],
    'name unique case insensitive' => [CreateContractRequestFactory::new()->state(['name' => 'MASTER AGREEMENT']), ['name' => 'unique']],
]);

// TODO: Cleanup Task (contract-name-uniqueness): delete this describe and the test within
describe('name uniqueness feature flag', function (): void {
    it('does not apply the unique form rule when the feature is disabled', function (): void {
        ContractNameUniquenessFeature::deactivate();
        DB::statement('DROP INDEX IF EXISTS contracts_name_unique');

        asSuperAdmin();

        Contract::factory()->create(['name' => 'Master Agreement']);

        $request = CreateContractRequestFactory::new()->state(['name' => 'Master Agreement'])->create();

        Livewire::test(CreateContract::class)
            ->fillForm($request)
            ->call('create')
            ->assertHasNoFormErrors();

        expect(Contract::query()->where('name', 'Master Agreement')->count())->toBe(2);
    });
});

describe('authorization', function (): void {
    it('denies access to the create page without the `contract.create` permission', function (): void {
        $user = User::factory()->create();
        $user->givePermissionTo('contract.view-any');
        actingAs($user);

        get(ContractResource::getUrl('create'))->assertForbidden();

        Livewire::test(CreateContract::class)->assertForbidden();
    });

    it('denies access to the create page when the contract management addon is disabled', function (): void {
        $settings = app(LicenseSettings::class);
        $settings->data->addons->contractManagement = false;
        $settings->save();

        $user = User::factory()->create();
        $user->givePermissionTo(['contract.view-any', 'contract.create']);
        actingAs($user);

        get(ContractResource::getUrl('create'))->assertForbidden();
    });

    it('allows access to the create page with the `contract.create` permission', function (): void {
        $user = User::factory()->create();
        $user->givePermissionTo(['contract.view-any', 'contract.create']);
        actingAs($user);

        get(ContractResource::getUrl('create'))->assertSuccessful();

        Livewire::test(CreateContract::class)->assertSuccessful();
    });
});
