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

use AidingApp\ServiceManagement\Filament\Resources\ChangeRequestTypes\Pages\CreateChangeRequestType;
use AidingApp\ServiceManagement\Models\ChangeRequestType;
use AidingApp\ServiceManagement\Tests\Tenant\Filament\Resources\ChangeRequestTypes\RequestFactories\CreateChangeRequestTypeRequestFactory;
use App\Features\ChangeRequestTypeAndStatusNameUniquenessFeature;
use App\Filament\Forms\Components\UserSelect;
use App\Models\Authenticatable;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\Config;
use Livewire\Livewire;

use function Pest\Laravel\assertDatabaseHas;
use function Tests\asSuperAdmin;

it('can create a change request type', function (): void {
    asSuperAdmin();

    $request = CreateChangeRequestTypeRequestFactory::new()->create();

    Livewire::test(CreateChangeRequestType::class)
        ->fillForm($request)
        ->call('create')
        ->assertHasNoFormErrors();

    assertDatabaseHas(ChangeRequestType::class, ['name' => $request['name']]);
});

it('can create a change request type with the name of a soft deleted change request type', function (): void {
    asSuperAdmin();

    ChangeRequestType::factory()->create(['name' => 'Major Change'])->delete();

    $request = CreateChangeRequestTypeRequestFactory::new()->state(['name' => 'Major Change'])->create();

    Livewire::test(CreateChangeRequestType::class)
        ->fillForm($request)
        ->call('create')
        ->assertHasNoFormErrors();

    expect(ChangeRequestType::query()->where('name', 'Major Change')->count())->toBe(1);
});

it('validates the inputs', function (CreateChangeRequestTypeRequestFactory $data, array $errors): void {
    asSuperAdmin();

    ChangeRequestType::factory()->create(['name' => 'Major Change']);

    Livewire::test(CreateChangeRequestType::class)
        ->fillForm($data->create())
        ->call('create')
        ->assertHasFormErrors($errors);
})->with([
    'name required' => [CreateChangeRequestTypeRequestFactory::new()->state(['name' => null]), ['name' => 'required']],
    'name max' => [CreateChangeRequestTypeRequestFactory::new()->state(['name' => str()->random(256)]), ['name' => 'max']],
    'name unique' => [CreateChangeRequestTypeRequestFactory::new()->state(['name' => 'Major Change']), ['name' => 'unique']],
    'name unique case insensitive' => [CreateChangeRequestTypeRequestFactory::new()->state(['name' => 'MAJOR CHANGE']), ['name' => 'unique']],
]);

// TODO: Cleanup Task (change-request-type-and-status-name-uniqueness): delete this describe and the tests within
describe('name uniqueness feature flag', function (): void {
    it('does not apply the unique form rule when the feature is disabled', function (): void {
        ChangeRequestTypeAndStatusNameUniquenessFeature::deactivate();

        asSuperAdmin();

        ChangeRequestType::factory()->create(['name' => 'Major Change']);

        $request = CreateChangeRequestTypeRequestFactory::new()->state(['name' => 'Major Change'])->create();

        expect(fn () => Livewire::test(CreateChangeRequestType::class)
            ->fillForm($request)
            ->call('create'))
            ->toThrow(UniqueConstraintViolationException::class);
    });
});

// UserSelect (userApprovers field) admin-filtering tests
// No pre-selected scenario on Create — no persisted record exists yet.

test('userApprovers UserSelect does not show admin users in options by default on CreateChangeRequestType', function (): void {
    asSuperAdmin();

    $regularUser = User::factory()->create();
    $adminUser = User::factory()->create();
    $adminUser->assignRole(Authenticatable::SUPER_ADMIN_ROLE);

    Livewire::test(CreateChangeRequestType::class)
        ->assertSuccessful()
        ->assertFormFieldExists('userApprovers', function (UserSelect $field) use ($regularUser, $adminUser): bool {
            return ! empty($field->getSearchResults($regularUser->full_name))
                && empty($field->getSearchResults($adminUser->full_name));
        });
});

test('userApprovers UserSelect shows all users when filter_admins_from_selection config is false on CreateChangeRequestType', function (): void {
    Config::set('app.filter_admins_from_selection', false);
    asSuperAdmin();

    $adminUser = User::factory()->create();
    $adminUser->assignRole(Authenticatable::SUPER_ADMIN_ROLE);

    Livewire::test(CreateChangeRequestType::class)
        ->assertSuccessful()
        ->assertFormFieldExists('userApprovers', function (UserSelect $field) use ($adminUser): bool {
            return ! empty($field->getSearchResults($adminUser->full_name));
        });
});
