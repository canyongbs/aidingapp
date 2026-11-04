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

use AidingApp\Contact\Filament\Resources\OrganizationResource\Pages\ListOrganizations;
use AidingApp\Contact\Models\Organization;
use App\Features\OrganizationArchivingFeature;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('only shows the bulk archive action to a user with the organization.delete permission', function (): void {
    Organization::factory(15)->create();

    $user = User::factory()
        ->create()
        ->givePermissionTo('organization.view-any', 'organization.*.view');

    actingAs($user);

    Livewire::test(ListOrganizations::class)
        ->assertActionHidden(TestAction::make('archive')->table()->bulk());

    $user->givePermissionTo('organization.*.delete');

    Livewire::test(ListOrganizations::class)
        ->assertActionVisible(TestAction::make('archive')->table()->bulk());
});

it('bulk archives organizations', function (): void {
    actingAs(User::factory()->create()->givePermissionTo('organization.view-any', 'organization.*.view', 'organization.*.delete'));

    $organizations = Organization::factory(2)->create();

    Livewire::test(ListOrganizations::class)
        ->selectTableRecords($organizations->modelKeys())
        ->callAction(TestAction::make('archive')->table()->bulk());

    [$first, $second] = $organizations->all();

    expect($first->refresh()->isArchived())->toBeTrue()
        ->and($second->refresh()->isArchived())->toBeTrue();
});

it('shows the bulk delete action when the organization archiving feature is inactive', function (): void {
    OrganizationArchivingFeature::deactivate();

    Organization::factory(3)->create();

    $user = User::factory()
        ->create()
        ->givePermissionTo('organization.view-any', 'organization.*.view');

    actingAs($user);

    Livewire::test(ListOrganizations::class)
        ->assertActionHidden(TestAction::make('delete')->table()->bulk());

    $user->givePermissionTo('organization.*.delete');

    Livewire::test(ListOrganizations::class)
        ->assertActionVisible(TestAction::make('delete')->table()->bulk())
        ->assertActionDoesNotExist(TestAction::make('archive')->table()->bulk());
});

it('only shows the import and export actions to a user with the `organization.import` permission', function (): void {
    $user = User::factory()
        ->create()
        ->givePermissionTo('organization.view-any', 'organization.*.view');

    actingAs($user);

    Livewire::test(ListOrganizations::class)
        ->assertActionHidden('import')
        ->assertActionHidden('export');

    $user->givePermissionTo('organization.import');

    Livewire::test(ListOrganizations::class)
        ->assertActionVisible('import')
        ->assertActionVisible('export');
});

describe('archived organizations', function (): void {
    beforeEach(function (): void {
        actingAs(User::factory()->create()->givePermissionTo('organization.view-any', 'organization.*.view'));
    });

    it('does not list archived organizations', function (): void {
        $organization = Organization::factory()->create();
        $archivedOrganization = Organization::factory()->archived()->create();

        Livewire::test(ListOrganizations::class)
            ->assertCanSeeTableRecords([$organization])
            ->assertCanNotSeeTableRecords([$archivedOrganization]);
    });

    it('does not find archived organizations in table search', function (): void {
        $organization = Organization::factory()->create(['name' => 'Searchable Active']);
        $archivedOrganization = Organization::factory()->archived()->create(['name' => 'Searchable Archived']);

        Livewire::test(ListOrganizations::class)
            ->searchTable('Searchable')
            ->assertCanSeeTableRecords([$organization])
            ->assertCanNotSeeTableRecords([$archivedOrganization]);
    });
});
