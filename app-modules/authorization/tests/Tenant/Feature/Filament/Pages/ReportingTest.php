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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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

use AidingApp\Authorization\Filament\Pages\Reporting;
use AidingApp\Department\Models\Department;
use AidingApp\Report\Enums\ReportAccessKey;
use AidingApp\Report\Models\ReportDepartmentAccess;
use AidingApp\Report\Models\ReportUserAccess;
use App\Features\ReportingFeature;
use App\Models\User;
use App\Settings\LicenseSettings;
use Filament\Actions\Testing\TestAction;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;

beforeEach(function () {
    ReportingFeature::activate();
});

it('cannot be accessed by a user without the reporting permission', function () {
    $user = User::factory()->create();

    actingAs($user);

    get(Reporting::getUrl())->assertForbidden();
});

it('can be accessed by a user with the reporting permission', function () {
    $user = User::factory()->create();

    $user->givePermissionTo('reporting.view-any');

    actingAs($user);

    get(Reporting::getUrl())->assertSuccessful();
});

it('always lists reports that require no feature addon', function () {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = false;
    $settings->data->addons->knowledgeManagement = false;
    $settings->data->addons->changeManagement = false;
    $settings->data->addons->assetManagement = false;
    $settings->data->addons->feedbackManagement = false;
    $settings->data->addons->contractManagement = false;
    $settings->data->addons->licenseManagement = false;
    $settings->data->addons->projectManagement = false;
    $settings->data->addons->advisoryManagement = false;
    $settings->data->addons->serviceMonitoring = false;
    $settings->save();

    $user = User::factory()->create();
    $user->givePermissionTo('reporting.view-any');

    actingAs($user);

    livewire(Reporting::class)
        ->assertCanSeeTableRecords([
            ReportAccessKey::AiSupportAssistant->value,
            ReportAccessKey::AiClarification->value,
            ReportAccessKey::AiResolution->value,
        ]);
});

it('only lists a report when the required feature addon is enabled for the tenant', function (Closure $enableReport, ReportAccessKey $case) {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->serviceManagement = false;
    $settings->data->addons->knowledgeManagement = false;
    $settings->data->addons->changeManagement = false;
    $settings->data->addons->assetManagement = false;
    $settings->data->addons->feedbackManagement = false;
    $settings->data->addons->contractManagement = false;
    $settings->data->addons->licenseManagement = false;
    $settings->data->addons->projectManagement = false;
    $settings->data->addons->advisoryManagement = false;
    $settings->data->addons->serviceMonitoring = false;
    $settings->save();

    $user = User::factory()->create();
    $user->givePermissionTo('reporting.view-any');

    actingAs($user);

    livewire(Reporting::class)
        ->assertCanNotSeeTableRecords([$case->value]);

    $enableReport($settings);
    $settings->save();

    livewire(Reporting::class)
        ->assertCanSeeTableRecords([$case->value]);
})->with([
    ReportAccessKey::ServiceRequests->value => [
        fn (LicenseSettings $settings) => $settings->data->addons->serviceManagement = true,
        ReportAccessKey::ServiceRequests,
    ],
    ReportAccessKey::ServiceRequestFeedback->value => [
        function (LicenseSettings $settings) {
            $settings->data->addons->serviceManagement = true;
            $settings->data->addons->feedbackManagement = true;
        },
        ReportAccessKey::ServiceRequestFeedback,
    ],
    ReportAccessKey::KnowledgeBase->value => [
        fn (LicenseSettings $settings) => $settings->data->addons->knowledgeManagement = true,
        ReportAccessKey::KnowledgeBase,
    ],
    ReportAccessKey::AssetManagement->value => [
        fn (LicenseSettings $settings) => $settings->data->addons->assetManagement = true,
        ReportAccessKey::AssetManagement,
    ],
    ReportAccessKey::AdvisoryManagement->value => [
        fn (LicenseSettings $settings) => $settings->data->addons->advisoryManagement = true,
        ReportAccessKey::AdvisoryManagement,
    ],
    ReportAccessKey::ContractManagement->value => [
        fn (LicenseSettings $settings) => $settings->data->addons->contractManagement = true,
        ReportAccessKey::ContractManagement,
    ],
    ReportAccessKey::LicenseManagement->value => [
        fn (LicenseSettings $settings) => $settings->data->addons->licenseManagement = true,
        ReportAccessKey::LicenseManagement,
    ],
    ReportAccessKey::ChangeManagement->value => [
        fn (LicenseSettings $settings) => $settings->data->addons->changeManagement = true,
        ReportAccessKey::ChangeManagement,
    ],
    ReportAccessKey::ServiceMonitoring->value => [
        fn (LicenseSettings $settings) => $settings->data->addons->serviceMonitoring = true,
        ReportAccessKey::ServiceMonitoring,
    ],
    ReportAccessKey::Projects->value => [
        fn (LicenseSettings $settings) => $settings->data->addons->projectManagement = true,
        ReportAccessKey::Projects,
    ],
    ReportAccessKey::TaskManagement->value => [
        fn (LicenseSettings $settings) => $settings->data->addons->projectManagement = true,
        ReportAccessKey::TaskManagement,
    ],
]);

it('can search reports by name', function () {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->projectManagement = true;
    $settings->save();

    $user = User::factory()->create();
    $user->givePermissionTo('reporting.view-any');

    actingAs($user);

    livewire(Reporting::class)
        ->searchTable(ReportAccessKey::Projects->getName())
        ->assertCanSeeTableRecords([ReportAccessKey::Projects->value])
        ->assertCanNotSeeTableRecords([ReportAccessKey::AiSupportAssistant->value]);
});

it('can filter reports by category', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('reporting.view-any');

    actingAs($user);

    livewire(Reporting::class)
        ->filterTable('category', ReportAccessKey::AiSupportAssistant->getCategory())
        ->assertCanSeeTableRecords([ReportAccessKey::AiSupportAssistant->value])
        ->assertCanNotSeeTableRecords([ReportAccessKey::Projects->value]);
});

it('manage report action is visible with appropriate permissions', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('reporting.view-any');
    $user->givePermissionTo('reporting.*.update');

    actingAs($user);

    livewire(Reporting::class)
        ->assertActionVisible(TestAction::make('manage')->table(ReportAccessKey::AiSupportAssistant->value));
});

it('can not manage report action without appropriate permissions', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('reporting.view-any');

    actingAs($user);

    livewire(Reporting::class)
        ->assertActionHidden(TestAction::make('manage')->table(ReportAccessKey::AiSupportAssistant->value));
});

it('assigns users to a report through the manage action', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('reporting.view-any');
    $user->givePermissionTo('reporting.*.update');

    $assignedUser = User::factory()->create();

    actingAs($user);

    livewire(Reporting::class)
        ->callAction(TestAction::make('manage')->table(ReportAccessKey::AiSupportAssistant->value), [
            'users' => [$assignedUser->getKey()],
            'departments' => [],
        ])
        ->assertNotified();

    expect(
        ReportUserAccess::query()
            ->where('report_key', ReportAccessKey::AiSupportAssistant->value)
            ->where('user_id', $assignedUser->getKey())
            ->exists()
    )->toBeTrue();
});

it('assigns departments to a report through the manage action', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('reporting.view-any');
    $user->givePermissionTo('reporting.*.update');

    $department = Department::factory()->create();

    actingAs($user);

    livewire(Reporting::class)
        ->callAction(TestAction::make('manage')->table(ReportAccessKey::AiSupportAssistant->value), [
            'users' => [],
            'departments' => [$department->getKey()],
        ])
        ->assertNotified();

    expect(
        ReportDepartmentAccess::query()
            ->where('report_key', ReportAccessKey::AiSupportAssistant->value)
            ->where('department_id', $department->getKey())
            ->exists()
    )->toBeTrue();
});

it('removes access that is no longer selected when managing a report', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('reporting.view-any');
    $user->givePermissionTo('reporting.*.update');

    $previouslyAssignedUser = User::factory()->create();

    ReportUserAccess::factory()->create([
        'report_key' => ReportAccessKey::AiSupportAssistant->value,
        'user_id' => $previouslyAssignedUser->getKey(),
    ]);

    actingAs($user);

    livewire(Reporting::class)
        ->callAction(TestAction::make('manage')->table(ReportAccessKey::AiSupportAssistant->value), [
            'users' => [],
            'departments' => [],
        ])
        ->assertNotified();

    expect(
        ReportUserAccess::query()
            ->where('report_key', ReportAccessKey::AiSupportAssistant->value)
            ->where('user_id', $previouslyAssignedUser->getKey())
            ->exists()
    )->toBeFalse();
});

it('counts a user with both direct and department access only once', function () {
    $department = Department::factory()->create();

    $user = User::factory()->create(['department_id' => $department->getKey()]);

    ReportUserAccess::factory()->create([
        'report_key' => ReportAccessKey::AiSupportAssistant->value,
        'user_id' => $user->getKey(),
    ]);

    ReportDepartmentAccess::factory()->create([
        'report_key' => ReportAccessKey::AiSupportAssistant->value,
        'department_id' => $department->getKey(),
    ]);

    expect(ReportAccessKey::AiSupportAssistant->accessCount())->toEqual(1);
});

it('bulk manage assignments action is visible for user with update permission', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('reporting.view-any');
    $user->givePermissionTo('reporting.*.update');

    actingAs($user);

    livewire(Reporting::class)
        ->assertTableBulkActionVisible('manageReportAssignments');
});

it('bulk manage assignments action is not visible for user without update permission', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('reporting.view-any');

    actingAs($user);

    livewire(Reporting::class)
        ->assertTableBulkActionHidden('manageReportAssignments');
});

it('bulk manage assignments action adds users and departments to selected reports additively', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('reporting.view-any');
    $user->givePermissionTo('reporting.*.update');

    $existingUser = User::factory()->create();
    $newUser = User::factory()->create();
    $department = Department::factory()->create();

    ReportUserAccess::factory()->create([
        'report_key' => ReportAccessKey::AiSupportAssistant->value,
        'user_id' => $existingUser->getKey(),
    ]);

    actingAs($user);

    livewire(Reporting::class)
        ->callTableBulkAction('manageReportAssignments', [ReportAccessKey::AiSupportAssistant->value], [
            'users' => [$newUser->getKey()],
            'departments' => [$department->getKey()],
            'sync' => false,
        ])
        ->assertNotified();

    expect(
        ReportUserAccess::query()
            ->where('report_key', ReportAccessKey::AiSupportAssistant->value)
            ->where('user_id', $existingUser->getKey())
            ->exists()
    )->toBeTrue();

    expect(
        ReportUserAccess::query()
            ->where('report_key', ReportAccessKey::AiSupportAssistant->value)
            ->where('user_id', $newUser->getKey())
            ->exists()
    )->toBeTrue();

    expect(
        ReportDepartmentAccess::query()
            ->where('report_key', ReportAccessKey::AiSupportAssistant->value)
            ->where('department_id', $department->getKey())
            ->exists()
    )->toBeTrue();
});

it('bulk manage assignments action replaces users and departments when sync is enabled', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('reporting.view-any');
    $user->givePermissionTo('reporting.*.update');

    $existingUser = User::factory()->create();
    $newUser = User::factory()->create();
    $existingDepartment = Department::factory()->create();
    $newDepartment = Department::factory()->create();

    ReportUserAccess::factory()->create([
        'report_key' => ReportAccessKey::AiSupportAssistant->value,
        'user_id' => $existingUser->getKey(),
    ]);

    ReportDepartmentAccess::factory()->create([
        'report_key' => ReportAccessKey::AiSupportAssistant->value,
        'department_id' => $existingDepartment->getKey(),
    ]);

    actingAs($user);

    livewire(Reporting::class)
        ->callTableBulkAction('manageReportAssignments', [ReportAccessKey::AiSupportAssistant->value], [
            'users' => [$newUser->getKey()],
            'departments' => [$newDepartment->getKey()],
            'sync' => true,
        ])
        ->assertNotified();

    expect(
        ReportUserAccess::query()
            ->where('report_key', ReportAccessKey::AiSupportAssistant->value)
            ->where('user_id', $existingUser->getKey())
            ->exists()
    )->toBeFalse();

    expect(
        ReportUserAccess::query()
            ->where('report_key', ReportAccessKey::AiSupportAssistant->value)
            ->where('user_id', $newUser->getKey())
            ->exists()
    )->toBeTrue();

    expect(
        ReportDepartmentAccess::query()
            ->where('report_key', ReportAccessKey::AiSupportAssistant->value)
            ->where('department_id', $existingDepartment->getKey())
            ->exists()
    )->toBeFalse();

    expect(
        ReportDepartmentAccess::query()
            ->where('report_key', ReportAccessKey::AiSupportAssistant->value)
            ->where('department_id', $newDepartment->getKey())
            ->exists()
    )->toBeTrue();
});
