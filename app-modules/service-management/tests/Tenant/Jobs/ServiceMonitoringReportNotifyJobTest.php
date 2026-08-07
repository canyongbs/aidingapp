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
use AidingApp\Notification\Notifications\Channels\DatabaseChannel;
use AidingApp\Notification\Notifications\Channels\MailChannel;
use AidingApp\ServiceManagement\Jobs\ServiceMonitoringReportNotifyJob;
use AidingApp\ServiceManagement\Models\ServiceMonitoringTarget;
use AidingApp\ServiceManagement\Notifications\ServiceMonitoringReportNotification;
use App\Models\User;
use Illuminate\Support\Facades\Notification;

it('sends notification to direct report users', function () {
    Notification::fake();

    $user = User::factory()->create();
    $serviceMonitorTarget = ServiceMonitoringTarget::factory()
        ->hasAttached($user, [], 'reportUsers')
        ->create(['is_reported_via_email' => true]);

    (new ServiceMonitoringReportNotifyJob($serviceMonitorTarget))->handle();

    Notification::assertSentTo(
        $user,
        ServiceMonitoringReportNotification::class
    );
});

it('sends notification to direct report contacts', function () {
    Notification::fake();

    $contact = Contact::factory()->create();
    $serviceMonitorTarget = ServiceMonitoringTarget::factory()
        ->hasAttached($contact, [], 'reportContacts')
        ->create(['is_reported_via_email' => true]);

    (new ServiceMonitoringReportNotifyJob($serviceMonitorTarget))->handle();

    Notification::assertSentTo(
        $contact,
        ServiceMonitoringReportNotification::class
    );
});

it('sends notification to users from report departments', function () {
    Notification::fake();

    $department = Department::factory()->create();
    $user = User::factory()->for($department, 'department')->create();

    $serviceMonitorTarget = ServiceMonitoringTarget::factory()
        ->hasAttached($department, [], 'reportDepartments')
        ->create(['is_reported_via_email' => true]);

    (new ServiceMonitoringReportNotifyJob($serviceMonitorTarget))->handle();

    Notification::assertSentTo(
        $user,
        ServiceMonitoringReportNotification::class
    );
});

it('merges users from direct users and departments without duplicates', function () {
    Notification::fake();

    $department = Department::factory()->create();
    $user1 = User::factory()->for($department, 'department')->create();
    $user2 = User::factory()->for($department, 'department')->create();

    $serviceMonitorTarget = ServiceMonitoringTarget::factory()
        ->hasAttached($user1, [], 'reportUsers')
        ->hasAttached($department, [], 'reportDepartments')
        ->create(['is_reported_via_email' => true]);

    (new ServiceMonitoringReportNotifyJob($serviceMonitorTarget))->handle();

    Notification::assertSentTo($user1, ServiceMonitoringReportNotification::class);
    Notification::assertSentTo($user2, ServiceMonitoringReportNotification::class);

    expect(Notification::sent($user1, ServiceMonitoringReportNotification::class))->toHaveCount(1);
});

it('sends notifications based on configured channels', function (bool $emailEnabled, bool $databaseEnabled, ?string $expectedChannel) {
    Notification::fake();

    $user = User::factory()->create();
    $contact = Contact::factory()->create();

    $serviceMonitorTarget = ServiceMonitoringTarget::factory()
        ->hasAttached($user, [], 'reportUsers')
        ->hasAttached($contact, [], 'reportContacts')
        ->create([
            'is_reported_via_email' => $emailEnabled,
            'is_reported_via_database' => $databaseEnabled,
        ]);

    (new ServiceMonitoringReportNotifyJob($serviceMonitorTarget))->handle();

    if ($expectedChannel === null) {
        Notification::assertNothingSent();
    } else {
        Notification::assertSentTo(
            $user,
            ServiceMonitoringReportNotification::class,
            fn (ServiceMonitoringReportNotification $notification) => $notification->channel === $expectedChannel
        );

        Notification::assertSentTo(
            $contact,
            ServiceMonitoringReportNotification::class,
            fn (ServiceMonitoringReportNotification $notification) => $notification->channel === $expectedChannel
        );
    }
})
    ->with([
        'both channels' => [
            true,
            true,
            'both',
        ],
        'database only' => [
            false,
            true,
            DatabaseChannel::class,
        ],
        'email only' => [
            true,
            false,
            MailChannel::class,
        ],
        'no channels' => [
            false,
            false,
            null,
        ],
    ]);

it('does not send notification when no recipients are configured', function () {
    Notification::fake();

    $serviceMonitorTarget = ServiceMonitoringTarget::factory()
        ->create(['is_reported_via_email' => true]);

    (new ServiceMonitoringReportNotifyJob($serviceMonitorTarget))->handle();

    Notification::assertNothingSent();
});

it('sends to all recipient types simultaneously', function () {
    Notification::fake();

    $user = User::factory()->create();
    $contact = Contact::factory()->create();
    $department = Department::factory()->create();
    $departmentUser = User::factory()->for($department, 'department')->create();

    $serviceMonitorTarget = ServiceMonitoringTarget::factory()
        ->hasAttached($user, [], 'reportUsers')
        ->hasAttached($contact, [], 'reportContacts')
        ->hasAttached($department, [], 'reportDepartments')
        ->create(['is_reported_via_email' => true]);

    (new ServiceMonitoringReportNotifyJob($serviceMonitorTarget))->handle();

    Notification::assertSentTo($user, ServiceMonitoringReportNotification::class);
    Notification::assertSentTo($contact, ServiceMonitoringReportNotification::class);
    Notification::assertSentTo($departmentUser, ServiceMonitoringReportNotification::class);
});
