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

use AidingApp\InventoryManagement\Filament\Resources\AssetLocations\Pages\ListAssetLocations;
use AidingApp\InventoryManagement\Models\AssetLocation;
use App\Features\AssetLocationNameUniquenessFeature;
use App\Models\User;
use Filament\Actions\EditAction;
use Filament\Actions\Testing\TestAction;
use Illuminate\Database\UniqueConstraintViolationException;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Tests\asSuperAdmin;

it('only shows the bulk delete action to a user with the asset_location.delete permission', function (): void {
    AssetLocation::factory(15)->create();

    $user = User::factory()
        ->create()
        ->givePermissionTo('asset_location.view-any', 'asset_location.*.view');

    actingAs($user);

    Livewire::test(ListAssetLocations::class)
        ->assertActionHidden(TestAction::make('delete')->table()->bulk());

    $user->givePermissionTo('asset_location.*.delete');

    Livewire::test(ListAssetLocations::class)
        ->assertActionVisible(TestAction::make('delete')->table()->bulk());
});

describe('edit action', function (): void {
    it('can update an asset location', function (): void {
        asSuperAdmin();

        $assetLocation = AssetLocation::factory()->create(['name' => 'Main Library']);

        Livewire::test(ListAssetLocations::class)
            ->callAction(TestAction::make(EditAction::class)->table($assetLocation), data: ['name' => 'Science Lab'])
            ->assertHasNoFormErrors();

        expect($assetLocation->refresh()->name)->toBe('Science Lab');
    });

    it('validates the inputs', function (array $data, array $errors): void {
        asSuperAdmin();

        AssetLocation::factory()->create(['name' => 'Main Library']);
        $assetLocation = AssetLocation::factory()->create();

        Livewire::test(ListAssetLocations::class)
            ->callAction(TestAction::make(EditAction::class)->table($assetLocation), data: $data)
            ->assertHasFormErrors($errors);
    })->with([
        'name required' => [['name' => null], ['name' => 'required']],
        'name max' => [['name' => str()->random(256)], ['name' => 'max']],
        'name unique' => [['name' => 'Main Library'], ['name' => 'unique']],
        'name unique case insensitive' => [['name' => 'MAIN LIBRARY'], ['name' => 'unique']],
    ]);

    it('allows keeping its own name with a different case', function (): void {
        asSuperAdmin();

        $assetLocation = AssetLocation::factory()->create(['name' => 'Main Library']);

        Livewire::test(ListAssetLocations::class)
            ->callAction(TestAction::make(EditAction::class)->table($assetLocation), data: ['name' => 'MAIN LIBRARY'])
            ->assertHasNoFormErrors();

        expect($assetLocation->refresh()->name)->toBe('MAIN LIBRARY');
    });

    it('allows the name of a soft deleted asset location', function (): void {
        asSuperAdmin();

        AssetLocation::factory()->create(['name' => 'Main Library'])->delete();
        $assetLocation = AssetLocation::factory()->create(['name' => 'Science Lab']);

        Livewire::test(ListAssetLocations::class)
            ->callAction(TestAction::make(EditAction::class)->table($assetLocation), data: ['name' => 'Main Library'])
            ->assertHasNoFormErrors();

        expect($assetLocation->refresh()->name)->toBe('Main Library');
    });
});

// TODO: Cleanup Task (asset-type-status-and-location-name-uniqueness): delete this describe and the test within
describe('name uniqueness feature flag', function (): void {
    it('does not apply the unique form rule when the feature is disabled', function (): void {
        AssetLocationNameUniquenessFeature::deactivate();

        asSuperAdmin();

        AssetLocation::factory()->create(['name' => 'Main Library']);
        $assetLocation = AssetLocation::factory()->create(['name' => 'Science Lab']);

        expect(fn () => Livewire::test(ListAssetLocations::class)
            ->callAction(TestAction::make(EditAction::class)->table($assetLocation), data: ['name' => 'Main Library']))
            ->toThrow(UniqueConstraintViolationException::class);
    });
});
