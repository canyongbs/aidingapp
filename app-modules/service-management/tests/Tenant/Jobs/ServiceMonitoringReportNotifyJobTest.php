<?php

use AidingApp\Notification\Notifications\Channels\DatabaseChannel;
use AidingApp\Notification\Notifications\Channels\MailChannel;
use AidingApp\ServiceManagement\Jobs\ServiceMonitoringReportNotifyJob;
use AidingApp\ServiceManagement\Models\ServiceMonitoringTarget;
use AidingApp\ServiceManagement\Notifications\ServiceMonitoringReportNotification;
use AidingApp\Contact\Models\Contact;
use AidingApp\Department\Models\Department;
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
