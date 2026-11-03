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

use AidingApp\ServiceManagement\Filament\Resources\ChangeRequestTypes\Pages\EditChangeRequestType;
use AidingApp\ServiceManagement\Models\ChangeRequestType;
use AidingApp\ServiceManagement\Tests\Tenant\Filament\Resources\ChangeRequestTypes\RequestFactories\EditChangeRequestTypeRequestFactory;
use App\Features\ChangeRequestTypeAndStatusNameUniquenessFeature;
use App\Filament\Forms\Components\UserSelect;
use App\Models\Authenticatable;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

use function Pest\Laravel\assertDatabaseHas;
use function Tests\asSuperAdmin;

it('can update a change request type', function (): void {
    asSuperAdmin();

    $changeRequestType = ChangeRequestType::factory()->create();
    $request = EditChangeRequestTypeRequestFactory::new()->create();

    Livewire::test(EditChangeRequestType::class, ['record' => $changeRequestType->getRouteKey()])
        ->fillForm($request)
        ->call('save')
        ->assertHasNoFormErrors();

    assertDatabaseHas(ChangeRequestType::class, ['id' => $changeRequestType->getKey(), 'name' => $request['name']]);
});

it('allows saving with a case-only change to the name of the current change request type', function (): void {
    asSuperAdmin();

    $changeRequestType = ChangeRequestType::factory()->create(['name' => 'Major Change']);

    Livewire::test(EditChangeRequestType::class, ['record' => $changeRequestType->getRouteKey()])
        ->fillForm(['name' => 'MAJOR CHANGE'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($changeRequestType->refresh()->name)->toBe('MAJOR CHANGE');
});

it('allows saving with the name of a soft deleted change request type', function (): void {
    asSuperAdmin();

    ChangeRequestType::factory()->create(['name' => 'Major Change'])->delete();
    $changeRequestType = ChangeRequestType::factory()->create(['name' => 'Minor Change']);

    Livewire::test(EditChangeRequestType::class, ['record' => $changeRequestType->getRouteKey()])
        ->fillForm(['name' => 'Major Change'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($changeRequestType->refresh()->name)->toBe('Major Change');
});

it('validates the inputs', function (EditChangeRequestTypeRequestFactory $data, array $errors): void {
    asSuperAdmin();

    ChangeRequestType::factory()->create(['name' => 'Major Change']);
    $changeRequestType = ChangeRequestType::factory()->create();

    Livewire::test(EditChangeRequestType::class, ['record' => $changeRequestType->getRouteKey()])
        ->fillForm($data->create())
        ->call('save')
        ->assertHasFormErrors($errors);
})->with([
    'name required' => [EditChangeRequestTypeRequestFactory::new()->state(['name' => null]), ['name' => 'required']],
    'name max' => [EditChangeRequestTypeRequestFactory::new()->state(['name' => str()->random(256)]), ['name' => 'max']],
    'name unique' => [EditChangeRequestTypeRequestFactory::new()->state(['name' => 'Major Change']), ['name' => 'unique']],
    'name unique case insensitive' => [EditChangeRequestTypeRequestFactory::new()->state(['name' => 'MAJOR CHANGE']), ['name' => 'unique']],
]);

describe('restoring', function (): void {
    it('restores a soft deleted change request type when its name is not in use', function (): void {
        asSuperAdmin();

        $changeRequestType = ChangeRequestType::factory()->create(['name' => 'Major Change']);
        $changeRequestType->delete();

        Livewire::test(EditChangeRequestType::class, ['record' => $changeRequestType->getRouteKey()])
            ->callAction('restore')
            ->assertNotified();

        expect($changeRequestType->refresh()->trashed())->toBeFalse();
    });

    it('does not restore a soft deleted change request type whose name is now used by another change request type', function (): void {
        asSuperAdmin();

        $changeRequestType = ChangeRequestType::factory()->create(['name' => 'Major Change']);
        $changeRequestType->delete();
        ChangeRequestType::factory()->create(['name' => 'MAJOR CHANGE']);

        Livewire::test(EditChangeRequestType::class, ['record' => $changeRequestType->getRouteKey()])
            ->callAction('restore')
            ->assertNotified('Unable to restore this change request type');

        expect($changeRequestType->refresh()->trashed())->toBeTrue();
    });
});

// TODO: Cleanup Task (change-request-type-and-status-name-uniqueness): delete this describe and the tests within
describe('name uniqueness feature flag', function (): void {
    it('does not apply the unique form rule when the feature is disabled', function (): void {
        ChangeRequestTypeAndStatusNameUniquenessFeature::deactivate();

        asSuperAdmin();

        ChangeRequestType::factory()->create(['name' => 'Major Change']);
        $changeRequestType = ChangeRequestType::factory()->create(['name' => 'Minor Change']);

        expect(fn () => Livewire::test(EditChangeRequestType::class, ['record' => $changeRequestType->getRouteKey()])
            ->fillForm(['name' => 'Major Change'])
            ->call('save'))
            ->toThrow(UniqueConstraintViolationException::class);
    });

    it('allows editing other fields of a record that already shares its name with another record when the feature is disabled', function (): void {
        ChangeRequestTypeAndStatusNameUniquenessFeature::deactivate();
        DB::statement('DROP INDEX change_request_types_name_unique');

        asSuperAdmin();

        ChangeRequestType::factory()->create(['name' => 'Major Change']);
        $changeRequestType = ChangeRequestType::factory()->create(['name' => 'Major Change']);

        Livewire::test(EditChangeRequestType::class, ['record' => $changeRequestType->getRouteKey()])
            ->fillForm(['number_of_required_approvals' => '2'])
            ->call('save')
            ->assertHasNoFormErrors();

        expect(ChangeRequestType::query()->where('name', 'Major Change')->count())->toBe(2);
    });
});

// UserSelect (userApprovers field) admin-filtering tests

test('userApprovers UserSelect does not show admin users in options by default on EditChangeRequestType', function (): void {
    asSuperAdmin();

    $changeRequestType = ChangeRequestType::factory()->create();
    $regularUser = User::factory()->create();
    $adminUser = User::factory()->create();
    $adminUser->assignRole(Authenticatable::SUPER_ADMIN_ROLE);

    Livewire::test(EditChangeRequestType::class, ['record' => $changeRequestType->getRouteKey()])
        ->assertSuccessful()
        ->assertFormFieldExists('userApprovers', function (UserSelect $field) use ($regularUser, $adminUser): bool {
            return ! empty($field->getSearchResults($regularUser->full_name))
                && empty($field->getSearchResults($adminUser->full_name));
        });
});

test('userApprovers UserSelect shows a pre-selected admin user so they can be deselected on EditChangeRequestType', function (): void {
    asSuperAdmin();

    $changeRequestType = ChangeRequestType::factory()->create();
    $adminUser = User::factory()->create();
    $adminUser->assignRole(Authenticatable::SUPER_ADMIN_ROLE);
    $changeRequestType->userApprovers()->attach($adminUser);

    Livewire::test(EditChangeRequestType::class, ['record' => $changeRequestType->getRouteKey()])
        ->assertSuccessful()
        ->assertFormFieldExists('userApprovers', function (UserSelect $field) use ($adminUser): bool {
            return ! empty($field->getSearchResults($adminUser->full_name));
        });
});

test('userApprovers UserSelect shows all users when filter_admins_from_selection config is false on EditChangeRequestType', function (): void {
    Config::set('app.filter_admins_from_selection', false);
    asSuperAdmin();

    $changeRequestType = ChangeRequestType::factory()->create();
    $adminUser = User::factory()->create();
    $adminUser->assignRole(Authenticatable::SUPER_ADMIN_ROLE);

    Livewire::test(EditChangeRequestType::class, ['record' => $changeRequestType->getRouteKey()])
        ->assertSuccessful()
        ->assertFormFieldExists('userApprovers', function (UserSelect $field) use ($adminUser): bool {
            return ! empty($field->getSearchResults($adminUser->full_name));
        });
});
