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

use AidingApp\Authorization\Models\Role;
use App\Models\Authenticatable;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Pages\Dashboard;
use Filament\Support\Contracts\HasIcon;
use Illuminate\Support\Arr;

use function Pest\Laravel\actingAs;
use function PHPUnit\Framework\assertCount;

test('there is only the Dashboard item for unlicensed users', function () {
    actingAs(User::factory()->create());

    $navigation = Filament::getNavigation();

    assertCount(1, $navigation);
    assertCount(1, Arr::first($navigation)->getItems());
});

test('navigation groups with a label must have an icon, and the unlabeled home group must not', function () {
    $user = User::factory()->create();
    $user->assignRole(Role::query()->where('name', Authenticatable::SUPER_ADMIN_ROLE)->firstOrFail());

    actingAs($user);

    $navigation = Filament::getNavigation();

    expect($navigation)->not()->toBeEmpty();

    foreach ($navigation as $group) {
        if (filled($group->getLabel())) {
            expect($group->getIcon())
                ->not()->toBeEmpty(
                    "Navigation group '{$group->getLabel()}' has a label but is missing an icon. Labeled groups must have a sidebar icon."
                );
        } else {
            expect($group->getIcon())
                ->toBeEmpty(
                    'The unlabeled (Home) navigation group should not have a group-level icon.'
                );
        }
    }
});

test('navigation items in labeled groups must not have an icon, while items in the home group must have one', function () {
    $user = User::factory()->create();
    $user->assignRole(Role::query()->where('name', Authenticatable::SUPER_ADMIN_ROLE)->firstOrFail());

    actingAs($user);

    $navigation = Filament::getNavigation();

    expect($navigation)->not()->toBeEmpty();

    foreach ($navigation as $group) {
        $groupIsLabeled = filled($group->getLabel());

        foreach ($group->getItems() as $item) {
            if ($groupIsLabeled) {
                expect($item->getIcon())
                    ->toBeEmpty(
                        "Navigation item '{$item->getLabel()}' in group '{$group->getLabel()}' must not have an icon. Icons belong on the group, not its items."
                    );
            } else {
                expect($item->getIcon())
                    ->not()->toBeEmpty(
                        "Navigation item '{$item->getLabel()}' in the unlabeled (Home) group must have an icon, as it appears directly in the sidebar."
                    );
            }
        }
    }
});

test('cluster sub-navigation groups must be defined via a dedicated enum, never a string literal', function () {
    $clusters = Filament::getCurrentOrDefaultPanel()->getClusters();

    expect($clusters)->not()->toBeEmpty();

    $offenders = [];

    foreach ($clusters as $clusterClass) {
        foreach ($clusterClass::getClusteredComponents() as $component) {
            $group = $component::getNavigationGroup();

            if ($group === null) {
                continue;
            }

            if (is_string($group)) {
                $offenders[] = "  - {$component} (in {$clusterClass}) uses string '{$group}'";
            }
        }
    }

    expect($offenders)->toBeEmpty(
        "The following clustered components use a string literal for \$navigationGroup:\n"
        . implode("\n", $offenders) . "\n\n"
        . 'Cluster sub-group names must be defined via a dedicated HasLabel-only enum '
        . '(e.g. ReportLibraryNavigationGroup, ServiceManagementAdministrationNavigationGroup, CommunicationNavigationGroup) '
        . 'so the convention is type-safe and discoverable. '
        . 'Either reuse an existing cluster enum or add a new case / new enum for this cluster.'
    );
});

test('cluster sub-navigation must use a HasLabel-only enum, never an enum that carries icons', function () {
    $clusters = Filament::getCurrentOrDefaultPanel()->getClusters();

    expect($clusters)->not()->toBeEmpty();

    $offenders = [];

    foreach ($clusters as $clusterClass) {
        foreach ($clusterClass::getClusteredComponents() as $component) {
            $group = $component::getNavigationGroup();

            if (! $group instanceof UnitEnum) {
                continue;
            }

            if ($group instanceof HasIcon) {
                $offenders[] = "  - {$component} (in {$clusterClass}) uses " . $group::class . "::{$group->name}";
            }
        }
    }

    expect($offenders)->toBeEmpty(
        "The following clustered components use a sub-group enum that implements HasIcon:\n"
        . implode("\n", $offenders) . "\n\n"
        . 'Cluster sub-group enums must implement HasLabel only — using the main NavigationGroup enum '
        . '(or any enum with HasIcon) renders an icon next to the sub-group heading in the cluster sub-nav, '
        . 'inconsistent with sibling sub-groups. Use a dedicated HasLabel-only enum (e.g. ReportLibraryNavigationGroup) instead.'
    );
});

test('Dashboard subclasses inside clusters must explicitly declare $navigationIcon to suppress the Dashboard home-icon fallback', function () {
    $clusters = Filament::getCurrentOrDefaultPanel()->getClusters();

    expect($clusters)->not()->toBeEmpty();

    $offenders = [];

    foreach ($clusters as $clusterClass) {
        foreach ($clusterClass::getClusteredComponents() as $component) {
            if (! is_subclass_of($component, Dashboard::class)) {
                continue;
            }

            $iconValue = (new ReflectionClass($component))
                ->getProperty('navigationIcon')
                ->getValue();

            if ($iconValue === null) {
                $offenders[] = "  - {$component} (in {$clusterClass})";
            }
        }
    }

    expect($offenders)->toBeEmpty(
        "The following Dashboard subclasses inside clusters do not explicitly declare \$navigationIcon:\n"
        . implode("\n", $offenders) . "\n\n"
        . "When \$navigationIcon is null on a Dashboard subclass, the parent's getNavigationIcon() falls back to a home icon, "
        . 'which leaks into the cluster sub-nav. '
        . "Either suppress it (`protected static string | BackedEnum | null \$navigationIcon = '';`) "
        . 'or set a deliberate icon.'
    );
});
