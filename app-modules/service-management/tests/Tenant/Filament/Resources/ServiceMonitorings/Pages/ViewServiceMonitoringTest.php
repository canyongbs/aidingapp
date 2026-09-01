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

use AidingApp\Contact\Models\Contact;
use AidingApp\Department\Models\Department;
use AidingApp\ServiceManagement\Enums\MonitorType;
use AidingApp\ServiceManagement\Enums\ServiceMonitoringReportFrequency;
use AidingApp\ServiceManagement\Filament\Resources\ServiceMonitorings\Pages\ViewServiceMonitoring;
use AidingApp\ServiceManagement\Filament\Resources\ServiceMonitorings\ServiceMonitoringResource;
use AidingApp\ServiceManagement\Models\ServiceMonitoringTarget;
use App\Models\User;
use App\Settings\LicenseSettings;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

test('ViewServiceMonitoring is gated with proper access control', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->addons->serviceMonitoring = false;
    $settings->save();
    $user = User::factory()->create();

    $serviceMonitoringTarget = ServiceMonitoringTarget::factory()->create();

    actingAs($user)
        ->get(
            ServiceMonitoringResource::getUrl('view', [
                'record' => $serviceMonitoringTarget,
            ])
        )->assertForbidden();

    $user->givePermissionTo('service_monitoring.view-any');
    $user->givePermissionTo('service_monitoring.*.view');

    actingAs($user)
        ->get(
            ServiceMonitoringResource::getUrl('view', [
                'record' => $serviceMonitoringTarget,
            ])
        )->assertForbidden();

    $settings->data->addons->serviceMonitoring = true;
    $settings->save();

    actingAs($user)
        ->get(
            ServiceMonitoringResource::getUrl('view', [
                'record' => $serviceMonitoringTarget,
            ])
        )->assertSuccessful();
});

test('The correct details are displayed on the ViewServiceMonitoring page', function () {
    $reportDepartment = Department::factory()->create();
    $reportUser = User::factory()->create();
    $reportContact = Contact::factory()->create();

    $serviceMonitoringTarget = ServiceMonitoringTarget::factory()
        ->hasAttached(Department::factory())
        ->hasAttached(User::factory())
        ->hasAttached($reportDepartment, [], 'reportDepartments')
        ->hasAttached($reportUser, [], 'reportUsers')
        ->hasAttached($reportContact, [], 'reportContacts')
        ->create([
            'is_reporting_active' => true,
            'report_frequency' => ServiceMonitoringReportFrequency::Weekly,
            'is_reported_via_email' => true,
            'is_reported_via_database' => true,
        ]);

    asSuperAdmin()
        ->get(
            ServiceMonitoringResource::getUrl('view', [
                'record' => $serviceMonitoringTarget,
            ])
        )
        ->assertSuccessful()
        ->assertSeeInOrder(
            [
                'Name',
                $serviceMonitoringTarget->name,
                'Description',
                $serviceMonitoringTarget->description,
                'URL',
                $serviceMonitoringTarget->domain,
                'Frequency',
                $serviceMonitoringTarget->frequency->getLabel(),
                'Departments',
                ...$serviceMonitoringTarget->departments()->pluck('name')->all(),
                'Users',
                ...$serviceMonitoringTarget->users()->pluck('name')->all(),
            ]
        )
        ->assertSee('Automated Reporting')
        ->assertSee('Frequency')
        ->assertSee($serviceMonitoringTarget->report_frequency->getLabel())
        ->assertSee('Email')
        ->assertSee('Application')
        ->assertSee($reportUser->name)
        ->assertSee($reportDepartment->name)
        ->assertSee($reportContact->full_name);
});

test('The Automated Reporting section is hidden when reporting is not active', function () {
    $reportDepartment = Department::factory()->create();
    $reportUser = User::factory()->create();
    $reportContact = Contact::factory()->create();

    $serviceMonitoringTarget = ServiceMonitoringTarget::factory()
        ->hasAttached($reportDepartment, [], 'reportDepartments')
        ->hasAttached($reportUser, [], 'reportUsers')
        ->hasAttached($reportContact, [], 'reportContacts')
        ->create([
            'is_reporting_active' => false,
            'report_frequency' => ServiceMonitoringReportFrequency::Weekly,
            'is_reported_via_email' => true,
            'is_reported_via_database' => true,
        ]);

    asSuperAdmin()
        ->get(
            ServiceMonitoringResource::getUrl('view', [
                'record' => $serviceMonitoringTarget,
            ])
        )
        ->assertSuccessful()
        ->assertDontSee('Automated Reporting')
        ->assertDontSee($reportUser->name)
        ->assertDontSee($reportDepartment->name)
        ->assertDontSee($reportContact->full_name);
});

test('keyword match values are displayed only for keyword monitors', function () {
    $keywordMonitor = ServiceMonitoringTarget::factory()->create([
        'monitor_type' => MonitorType::KeywordMatch,
        'should_contain' => ['test 1', 'test 2'],
        'should_not_contain' => ['test 3', 'test 4'],
    ]);

    asSuperAdmin()
        ->get(ServiceMonitoringResource::getUrl('view', ['record' => $keywordMonitor]))
        ->assertSuccessful()
        ->assertSeeInOrder([
            'Should Contain',
            'test 1',
            'test 2',
            'Should Not Contain',
            'test 3',
            'test 4',
        ]);

    $availabilityMonitor = ServiceMonitoringTarget::factory()->create([
        'monitor_type' => MonitorType::Availability,
        'should_contain' => ['test 1'],
        'should_not_contain' => ['test 2'],
    ]);

    asSuperAdmin()
        ->get(ServiceMonitoringResource::getUrl('view', ['record' => $availabilityMonitor]))
        ->assertSuccessful()
        ->assertDontSee('Should Contain')
        ->assertDontSee('Should Not Contain');
});

test('keyword match values with punctuation retain clear boundaries', function () {
    $keywordMonitor = ServiceMonitoringTarget::factory()->create([
        'monitor_type' => MonitorType::KeywordMatch,
        'should_contain' => ['test1', 'test 2', 'test 3, "test 4"', '"test 5, test 6"'],
    ]);

    asSuperAdmin()
        ->get(ServiceMonitoringResource::getUrl('view', ['record' => $keywordMonitor]))
        ->assertSuccessful()
        ->assertSeeInOrder([
            'test1',
            'test 2',
            'test 3, "test 4"',
            '"test 5, test 6"',
        ]);
});

test('Reset Monitoring button resets monitoring', function () {
    $user = User::factory()->create();

    asSuperAdmin($user);

    $serviceMonitoringTarget = ServiceMonitoringTarget::factory()->create();

    $serviceMonitoringTarget->histories()->create([
        'response' => 200,
        'response_time' => 0.138348,
        'succeeded' => 1,
    ]);

    expect($serviceMonitoringTarget->histories()->count())
        ->toBe(1);

    livewire(ViewServiceMonitoring::class, [
        'record' => $serviceMonitoringTarget->getRouteKey(),
    ])
        ->assertSuccessful()
        ->assertSee('Reset Monitoring')
        ->callAction('reset');

    expect($serviceMonitoringTarget->histories()->count())
        ->toBe(0);
});

test('a confidential service monitoring is only visible to its creator, granted users/departments, and admins', function () {
    $creator = User::factory()->create();
    $user = User::factory()->create();

    $user->givePermissionTo('service_monitoring.view-any');
    $user->givePermissionTo('service_monitoring.*.view');

    $serviceMonitoringTarget = ServiceMonitoringTarget::factory()
        ->confidential()
        ->for($creator, 'createdBy')
        ->create();

    actingAs($user);

    get(ServiceMonitoringResource::getUrl('view', ['record' => $serviceMonitoringTarget]))
        ->assertNotFound();

    $serviceMonitoringTarget->confidentialUsers()->attach($user->getKey());

    get(ServiceMonitoringResource::getUrl('view', ['record' => $serviceMonitoringTarget]))
        ->assertSuccessful();

    $serviceMonitoringTarget->confidentialUsers()->detach($user->getKey());

    get(ServiceMonitoringResource::getUrl('view', ['record' => $serviceMonitoringTarget]))
        ->assertNotFound();

    $department = Department::factory()->create();

    $user->department()->associate($department)->save();

    $serviceMonitoringTarget->confidentialDepartments()->attach($department->getKey());

    get(ServiceMonitoringResource::getUrl('view', ['record' => $serviceMonitoringTarget]))
        ->assertSuccessful();

    $serviceMonitoringTarget->confidentialDepartments()->detach($department->getKey());

    get(ServiceMonitoringResource::getUrl('view', ['record' => $serviceMonitoringTarget]))
        ->assertNotFound();

    $creator->givePermissionTo('service_monitoring.view-any');
    $creator->givePermissionTo('service_monitoring.*.view');

    actingAs($creator);

    get(ServiceMonitoringResource::getUrl('view', ['record' => $serviceMonitoringTarget]))
        ->assertSuccessful();

    asSuperAdmin();

    get(ServiceMonitoringResource::getUrl('view', ['record' => $serviceMonitoringTarget]))
        ->assertSuccessful();
});
