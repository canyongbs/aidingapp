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

use AidingApp\Department\Models\Department;
use AidingApp\Notification\Notifications\Channels\DatabaseChannel;
use AidingApp\Notification\Notifications\Channels\MailChannel;
use AidingApp\ServiceManagement\Enums\HttpMethod;
use AidingApp\ServiceManagement\Enums\MonitorType;
use AidingApp\ServiceManagement\Enums\ServiceMonitoringFrequency;
use AidingApp\ServiceManagement\Jobs\ServiceMonitoringCheckJob;
use AidingApp\ServiceManagement\Models\HistoricalServiceMonitoring;
use AidingApp\ServiceManagement\Models\ServiceMonitoringTarget;
use AidingApp\ServiceManagement\Notifications\ServiceMonitoringNotification;
use App\Features\ServiceMonitoringAuthTypeFeature;
use App\Models\User;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;

use function Pest\Laravel\assertDatabaseHas;

it('sends a notification if the response is not 200', function ($frequency) {
    Http::fake(function () {
        return Http::response('Test', 500);
    });
    Notification::fake();

    $user = User::factory()->create();

    $serviceMonitorTarget = ServiceMonitoringTarget::factory()
        ->hasAttached($user)
        ->create(['frequency' => $frequency, 'is_notified_via_email' => true]);

    (new ServiceMonitoringCheckJob($serviceMonitorTarget))->handle();

    Notification::assertSentTo(
        $user,
        ServiceMonitoringNotification::class
    );

    assertDatabaseHas(HistoricalServiceMonitoring::class, ['response' => 500, 'succeeded' => false, 'service_monitoring_target_id' => $serviceMonitorTarget->getKey()]);
})
    ->with(
        [
            fn () => ServiceMonitoringFrequency::OneHour,
            fn () => ServiceMonitoringFrequency::TwentyFourHours,
        ]
    );

it('does not send a notification if the response is 200', function ($frequency) {
    Http::fake(function () {
        return Http::response('Test', 200);
    });
    Notification::fake();

    $serviceMonitorTarget = ServiceMonitoringTarget::factory()
        ->hasAttached(User::factory()->create())
        ->create(['frequency' => $frequency]);

    (new ServiceMonitoringCheckJob($serviceMonitorTarget))->handle();

    Notification::assertNothingSent();

    assertDatabaseHas(HistoricalServiceMonitoring::class, ['response' => 200, 'succeeded' => true, 'service_monitoring_target_id' => $serviceMonitorTarget->getKey()]);
})
    ->with(
        [
            fn () => ServiceMonitoringFrequency::OneHour,
            fn () => ServiceMonitoringFrequency::TwentyFourHours,
        ]
    );

it('does not send a notification when all required keyword match values are present', function ($frequency) {
    Http::fake(fn () => Http::response('Test 1 and Test 2'));
    Notification::fake();

    $user = User::factory()->create();

    $serviceMonitorTarget = ServiceMonitoringTarget::factory()
        ->hasAttached($user)
        ->create([
            'monitor_type' => MonitorType::KeywordMatch,
            'should_contain' => ['Test 1', 'Test 2'],
            'frequency' => $frequency,
            'is_notified_via_email' => true,
        ]);

    (new ServiceMonitoringCheckJob($serviceMonitorTarget))->handle();

    Notification::assertNothingSent();

    assertDatabaseHas(HistoricalServiceMonitoring::class, [
        'response' => 200,
        'succeeded' => true,
        'service_monitoring_target_id' => $serviceMonitorTarget->getKey(),
    ]);
})->with(ServiceMonitoringFrequency::cases());

it('sends a notification when any required keyword match value is missing', function ($frequency) {
    Http::fakeSequence()
        ->push('Test 1')
        ->push('Test 2');
    Notification::fake();

    $user = User::factory()->create();

    $serviceMonitorTarget = ServiceMonitoringTarget::factory()
        ->hasAttached($user)
        ->create([
            'monitor_type' => MonitorType::KeywordMatch,
            'should_contain' => ['Test 1', 'Test 2'],
            'frequency' => $frequency,
            'is_notified_via_email' => true,
        ]);

    (new ServiceMonitoringCheckJob($serviceMonitorTarget))->handle();

    Notification::assertSentTo($user, ServiceMonitoringNotification::class);

    (new ServiceMonitoringCheckJob($serviceMonitorTarget))->handle();

    Notification::assertSentTo($user, ServiceMonitoringNotification::class);
})->with(ServiceMonitoringFrequency::cases());

it('sends a failure notification when both keyword match fields are empty', function ($frequency) {
    Http::preventStrayRequests();
    Notification::fake();

    $user = User::factory()->create();

    $serviceMonitorTarget = ServiceMonitoringTarget::factory()
        ->hasAttached($user)
        ->create([
            'monitor_type' => MonitorType::KeywordMatch,
            'should_contain' => [],
            'should_not_contain' => [],
            'frequency' => $frequency,
            'is_notified_via_email' => true,
        ]);

    (new ServiceMonitoringCheckJob($serviceMonitorTarget))->handle();

    Notification::assertSentTo($user, ServiceMonitoringNotification::class);

    assertDatabaseHas(HistoricalServiceMonitoring::class, [
        'response' => 0,
        'response_time' => 0,
        'succeeded' => false,
        'service_monitoring_target_id' => $serviceMonitorTarget->getKey(),
        'keyword_match_failures' => json_encode(['No keyword match values were configured.']),
    ]);
})->with(ServiceMonitoringFrequency::cases());

it('sends a notification when a prohibited keyword match value is present', function ($frequency) {
    Http::fake(fn () => Http::response('Test 1'));
    Notification::fake();

    $user = User::factory()->create();

    $serviceMonitorTarget = ServiceMonitoringTarget::factory()
        ->hasAttached($user)
        ->create([
            'monitor_type' => MonitorType::KeywordMatch,
            'should_contain' => [],
            'should_not_contain' => ['Test 1', 'Test 2'],
            'frequency' => $frequency,
            'is_notified_via_email' => true,
        ]);

    (new ServiceMonitoringCheckJob($serviceMonitorTarget))->handle();

    Notification::assertSentTo($user, ServiceMonitoringNotification::class);
})->with(ServiceMonitoringFrequency::cases());

it('does not send a notification when no prohibited keyword match values are present', function ($frequency) {
    Http::fake(fn () => Http::response('Test 3'));
    Notification::fake();

    $user = User::factory()->create();

    $serviceMonitorTarget = ServiceMonitoringTarget::factory()
        ->hasAttached($user)
        ->create([
            'monitor_type' => MonitorType::KeywordMatch,
            'should_contain' => [],
            'should_not_contain' => ['Test 1', 'Test 2'],
            'frequency' => $frequency,
            'is_notified_via_email' => true,
        ]);

    (new ServiceMonitoringCheckJob($serviceMonitorTarget))->handle();

    Notification::assertNothingSent();

    assertDatabaseHas(HistoricalServiceMonitoring::class, [
        'response' => 200,
        'succeeded' => true,
        'service_monitoring_target_id' => $serviceMonitorTarget->getKey(),
    ]);
})->with(ServiceMonitoringFrequency::cases());

it('passes when all required keyword match values are present and no prohibited values are present', function ($frequency) {
    Http::fakeSequence()
        ->push('Test 1 and Test 2')
        ->push('Test 1')
        ->push('Test 1 and Test 2 and Test 3');
    Notification::fake();

    $user = User::factory()->create();

    $serviceMonitorTarget = ServiceMonitoringTarget::factory()
        ->hasAttached($user)
        ->create([
            'monitor_type' => MonitorType::KeywordMatch,
            'should_contain' => ['Test 1', 'Test 2'],
            'should_not_contain' => ['Test 3', 'Test 4'],
            'frequency' => $frequency,
            'is_notified_via_email' => true,
        ]);

    (new ServiceMonitoringCheckJob($serviceMonitorTarget))->handle();

    Notification::assertNothingSent();

    assertDatabaseHas(HistoricalServiceMonitoring::class, [
        'response' => 200,
        'succeeded' => true,
        'service_monitoring_target_id' => $serviceMonitorTarget->getKey(),
    ]);

    (new ServiceMonitoringCheckJob($serviceMonitorTarget))->handle();

    Notification::assertSentTo($user, ServiceMonitoringNotification::class);

    (new ServiceMonitoringCheckJob($serviceMonitorTarget))->handle();

    Notification::assertSentTo($user, ServiceMonitoringNotification::class);
})->with(ServiceMonitoringFrequency::cases());

it('matches keyword values case-insensitively and treats special characters literally', function ($frequency) {
    Http::fakeSequence()
        ->push('test 1 sign-in sign_in test, one . * ? ! ( ) [ ]')
        ->push('test 1 sign in sign_in test, one . * ? ! ( ) [ ]');
    Notification::fake();

    $user = User::factory()->create();

    $serviceMonitorTarget = ServiceMonitoringTarget::factory()
        ->hasAttached($user)
        ->create([
            'monitor_type' => MonitorType::KeywordMatch,
            'should_contain' => ['TEST 1', 'sign-in', 'sign_in', 'test, one', '.', '*', '?', '!', '( )', '[ ]'],
            'frequency' => $frequency,
            'is_notified_via_email' => true,
        ]);

    (new ServiceMonitoringCheckJob($serviceMonitorTarget))->handle();

    Notification::assertNothingSent();

    (new ServiceMonitoringCheckJob($serviceMonitorTarget))->handle();

    Notification::assertSentTo($user, ServiceMonitoringNotification::class);
})->with(ServiceMonitoringFrequency::cases());

it('records each unique keyword match failure', function ($frequency) {
    Http::fake(fn () => Http::response('Test 1 and Test 4'));
    Notification::fake();

    $user = User::factory()->create();

    $serviceMonitorTarget = ServiceMonitoringTarget::factory()
        ->hasAttached($user)
        ->create([
            'monitor_type' => MonitorType::KeywordMatch,
            'should_contain' => ['Test 1', 'Test 2', 'Test 3'],
            'should_not_contain' => ['Test 4'],
            'frequency' => $frequency,
            'is_notified_via_email' => true,
        ]);

    (new ServiceMonitoringCheckJob($serviceMonitorTarget))->handle();

    Notification::assertSentTo($user, ServiceMonitoringNotification::class);

    $history = HistoricalServiceMonitoring::query()
        ->where('service_monitoring_target_id', $serviceMonitorTarget->getKey())
        ->latest()
        ->firstOrFail();

    expect($history->succeeded)->toBeFalse()
        ->and($history->keyword_match_failures)->toBe([
            'Required string not found: Test 2',
            'Required string not found: Test 3',
            'Prohibited string found: Test 4',
        ]);
})->with(ServiceMonitoringFrequency::cases());

it('sends a notification when a keyword match response has no readable body', function ($frequency) {
    Http::fake(fn () => Http::response('', 200));
    Notification::fake();

    $user = User::factory()->create();

    $serviceMonitorTarget = ServiceMonitoringTarget::factory()
        ->hasAttached($user)
        ->create([
            'monitor_type' => MonitorType::KeywordMatch,
            'should_contain' => ['Test 1'],
            'frequency' => $frequency,
            'is_notified_via_email' => true,
        ]);

    (new ServiceMonitoringCheckJob($serviceMonitorTarget))->handle();

    Notification::assertSentTo($user, ServiceMonitoringNotification::class);

    assertDatabaseHas(HistoricalServiceMonitoring::class, [
        'response' => 200,
        'succeeded' => false,
        'service_monitoring_target_id' => $serviceMonitorTarget->getKey(),
    ]);
})->with(ServiceMonitoringFrequency::cases());

it('matches keyword values across HTML tags', function ($frequency) {
    Http::fake(fn () => Http::response('<html><body><p>Sign <strong>in</strong></p></body></html>'));
    Notification::fake();

    $user = User::factory()->create();

    $serviceMonitorTarget = ServiceMonitoringTarget::factory()
        ->hasAttached($user)
        ->create([
            'monitor_type' => MonitorType::KeywordMatch,
            'should_contain' => ['sign in'],
            'frequency' => $frequency,
            'is_notified_via_email' => true,
        ]);

    (new ServiceMonitoringCheckJob($serviceMonitorTarget))->handle();

    Notification::assertNothingSent();

    assertDatabaseHas(HistoricalServiceMonitoring::class, [
        'response' => 200,
        'succeeded' => true,
        'service_monitoring_target_id' => $serviceMonitorTarget->getKey(),
    ]);
})->with(ServiceMonitoringFrequency::cases());

it('does not match keyword values across adjacent inline HTML elements', function ($frequency) {
    Http::fake(fn () => Http::response('<span>Sign</span><span>in</span>'));
    Notification::fake();

    $user = User::factory()->create();

    $serviceMonitorTarget = ServiceMonitoringTarget::factory()
        ->hasAttached($user)
        ->create([
            'monitor_type' => MonitorType::KeywordMatch,
            'should_contain' => ['sign in'],
            'frequency' => $frequency,
            'is_notified_via_email' => true,
        ]);

    (new ServiceMonitoringCheckJob($serviceMonitorTarget))->handle();

    Notification::assertSentTo($user, ServiceMonitoringNotification::class);

    $history = HistoricalServiceMonitoring::query()
        ->where('service_monitoring_target_id', $serviceMonitorTarget->getKey())
        ->latest()
        ->firstOrFail();

    expect($history->keyword_match_failures)->toBe([
        'Required string not found: sign in',
    ]);
})->with(ServiceMonitoringFrequency::cases());

it('matches keyword values across adjacent block HTML elements', function ($frequency) {
    Http::fake(fn () => Http::response('<p>Sign</p><p>in</p>'));
    Notification::fake();

    $user = User::factory()->create();

    $serviceMonitorTarget = ServiceMonitoringTarget::factory()
        ->hasAttached($user)
        ->create([
            'monitor_type' => MonitorType::KeywordMatch,
            'should_contain' => ['sign in'],
            'frequency' => $frequency,
            'is_notified_via_email' => true,
        ]);

    (new ServiceMonitoringCheckJob($serviceMonitorTarget))->handle();

    Notification::assertNothingSent();

    assertDatabaseHas(HistoricalServiceMonitoring::class, [
        'response' => 200,
        'succeeded' => true,
        'service_monitoring_target_id' => $serviceMonitorTarget->getKey(),
    ]);
})->with(ServiceMonitoringFrequency::cases());

it('matches keyword values separated by a line break element', function ($frequency) {
    Http::fake(fn () => Http::response('Sign<br>in'));
    Notification::fake();

    $user = User::factory()->create();

    $serviceMonitorTarget = ServiceMonitoringTarget::factory()
        ->hasAttached($user)
        ->create([
            'monitor_type' => MonitorType::KeywordMatch,
            'should_contain' => ['sign in'],
            'frequency' => $frequency,
            'is_notified_via_email' => true,
        ]);

    (new ServiceMonitoringCheckJob($serviceMonitorTarget))->handle();

    Notification::assertNothingSent();

    assertDatabaseHas(HistoricalServiceMonitoring::class, [
        'response' => 200,
        'succeeded' => true,
        'service_monitoring_target_id' => $serviceMonitorTarget->getKey(),
    ]);
})->with(ServiceMonitoringFrequency::cases());

it('matches keyword values across inline HTML elements with source whitespace', function ($frequency) {
    Http::fake(fn () => Http::response('<strong>sign</strong> <em>in</em>'));
    Notification::fake();

    $user = User::factory()->create();

    $serviceMonitorTarget = ServiceMonitoringTarget::factory()
        ->hasAttached($user)
        ->create([
            'monitor_type' => MonitorType::KeywordMatch,
            'should_contain' => ['sign in'],
            'frequency' => $frequency,
            'is_notified_via_email' => true,
        ]);

    (new ServiceMonitoringCheckJob($serviceMonitorTarget))->handle();

    Notification::assertNothingSent();

    assertDatabaseHas(HistoricalServiceMonitoring::class, [
        'response' => 200,
        'succeeded' => true,
        'service_monitoring_target_id' => $serviceMonitorTarget->getKey(),
    ]);
})->with(ServiceMonitoringFrequency::cases());

it('matches keyword values separated by a non-breaking space', function ($frequency) {
    Http::fake(fn () => Http::response('Sign&nbsp;in'));
    Notification::fake();

    $user = User::factory()->create();

    $serviceMonitorTarget = ServiceMonitoringTarget::factory()
        ->hasAttached($user)
        ->create([
            'monitor_type' => MonitorType::KeywordMatch,
            'should_contain' => ['sign in'],
            'frequency' => $frequency,
            'is_notified_via_email' => true,
        ]);

    (new ServiceMonitoringCheckJob($serviceMonitorTarget))->handle();

    Notification::assertNothingSent();

    assertDatabaseHas(HistoricalServiceMonitoring::class, [
        'response' => 200,
        'succeeded' => true,
        'service_monitoring_target_id' => $serviceMonitorTarget->getKey(),
    ]);
})->with(ServiceMonitoringFrequency::cases());

it('preserves block boundaries in minified HTML', function ($frequency) {
    Http::fake(fn () => Http::response('<div>Sign</div><div>in</div>'));
    Notification::fake();

    $user = User::factory()->create();

    $serviceMonitorTarget = ServiceMonitoringTarget::factory()
        ->hasAttached($user)
        ->create([
            'monitor_type' => MonitorType::KeywordMatch,
            'should_contain' => ['sign in'],
            'frequency' => $frequency,
            'is_notified_via_email' => true,
        ]);

    (new ServiceMonitoringCheckJob($serviceMonitorTarget))->handle();

    Notification::assertNothingSent();

    assertDatabaseHas(HistoricalServiceMonitoring::class, [
        'response' => 200,
        'succeeded' => true,
        'service_monitoring_target_id' => $serviceMonitorTarget->getKey(),
    ]);
})->with(ServiceMonitoringFrequency::cases());

it('excludes script, style, and noscript content from keyword matching', function ($frequency) {
    Http::fake(fn () => Http::response('<script>sign in</script><style>sign in</style><noscript>sign in</noscript>'));
    Notification::fake();

    $user = User::factory()->create();

    $serviceMonitorTarget = ServiceMonitoringTarget::factory()
        ->hasAttached($user)
        ->create([
            'monitor_type' => MonitorType::KeywordMatch,
            'should_contain' => ['sign in'],
            'frequency' => $frequency,
            'is_notified_via_email' => true,
        ]);

    (new ServiceMonitoringCheckJob($serviceMonitorTarget))->handle();

    Notification::assertSentTo($user, ServiceMonitoringNotification::class);

    $history = HistoricalServiceMonitoring::query()
        ->where('service_monitoring_target_id', $serviceMonitorTarget->getKey())
        ->latest()
        ->firstOrFail();

    expect($history->keyword_match_failures)->toBe([
        'Required string not found: sign in',
    ]);
})->with(ServiceMonitoringFrequency::cases());

it('handles unresolvable host errors gracefully', function ($frequency) {
    Http::fake(function () {
        throw new ConnectionException('Could not resolve host');
    });
    Notification::fake();

    $user = User::factory()->create();

    $serviceMonitorTarget = ServiceMonitoringTarget::factory()
        ->hasAttached($user)
        ->create(['frequency' => $frequency, 'is_notified_via_email' => true]);

    (new ServiceMonitoringCheckJob($serviceMonitorTarget))->handle();

    Notification::assertSentTo(
        $user,
        ServiceMonitoringNotification::class
    );

    assertDatabaseHas(HistoricalServiceMonitoring::class, ['response' => 523, 'succeeded' => false, 'service_monitoring_target_id' => $serviceMonitorTarget->getKey()]);
})
    ->with(
        [
            fn () => ServiceMonitoringFrequency::OneHour,
            fn () => ServiceMonitoringFrequency::TwentyFourHours,
        ]
    );

it('sends a notification when a keyword match response has a binary content type', function ($frequency) {
    Http::fake(fn () => Http::response('Test 1', 200, ['Content-Type' => 'application/octet-stream']));
    Notification::fake();

    $user = User::factory()->create();

    $serviceMonitorTarget = ServiceMonitoringTarget::factory()
        ->hasAttached($user)
        ->create([
            'monitor_type' => MonitorType::KeywordMatch,
            'should_contain' => [],
            'should_not_contain' => ['Test 1'],
            'frequency' => $frequency,
            'is_notified_via_email' => true,
        ]);

    (new ServiceMonitoringCheckJob($serviceMonitorTarget))->handle();

    Notification::assertSentTo($user, ServiceMonitoringNotification::class);

    $history = HistoricalServiceMonitoring::query()
        ->where('service_monitoring_target_id', $serviceMonitorTarget->getKey())
        ->latest()
        ->firstOrFail();

    expect($history->keyword_match_failures)->toBe([
        'The response has an unreadable content type.',
    ]);
})->with(ServiceMonitoringFrequency::cases());

it('sends a notification when a keyword match response is an Incapsula challenge page', function () {
    Http::fake(fn () => Http::response('<iframe src="/_Incapsula_Resource">Request unsuccessful. Incapsula</iframe>', 200));
    Notification::fake();

    $user = User::factory()->create();

    $serviceMonitorTarget = ServiceMonitoringTarget::factory()
        ->hasAttached($user)
        ->create([
            'monitor_type' => MonitorType::KeywordMatch,
            'should_contain' => ['healthy'],
            'is_notified_via_email' => true,
        ]);

    (new ServiceMonitoringCheckJob($serviceMonitorTarget))->handle();

    Notification::assertSentTo($user, ServiceMonitoringNotification::class);

    $history = HistoricalServiceMonitoring::query()
        ->where('service_monitoring_target_id', $serviceMonitorTarget->getKey())
        ->latest()
        ->firstOrFail();

    expect($history->succeeded)->toBeFalse()
        ->and($history->keyword_match_failures)->toBe([
            'The response was blocked by an Incapsula challenge page.',
        ]);
});

it('sends notifications based on configured channels', function (
    bool $emailEnabled,
    bool $databaseEnabled,
    ?string $expectedChannel
) {
    Http::fake(fn () => Http::response('Test', 500));
    Notification::fake();

    $user = User::factory()->create();

    $serviceMonitorTarget = ServiceMonitoringTarget::factory()
        ->hasAttached($user)
        ->create([
            'frequency' => ServiceMonitoringFrequency::OneHour,
            'is_notified_via_email' => $emailEnabled,
            'is_notified_via_database' => $databaseEnabled,
        ]);

    (new ServiceMonitoringCheckJob($serviceMonitorTarget))->handle();

    if ($expectedChannel === null) {
        Notification::assertNothingSent();
    } else {
        Notification::assertSentTo(
            $user,
            ServiceMonitoringNotification::class,
            fn (ServiceMonitoringNotification $notification) => $notification->channel === $expectedChannel
        );
    }

    assertDatabaseHas(HistoricalServiceMonitoring::class, [
        'response' => 500,
        'succeeded' => false,
        'service_monitoring_target_id' => $serviceMonitorTarget->getKey(),
    ]);
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

it('sends a notification for a confidential service monitor to a recipient granted confidential access', function () {
    Http::fake(fn () => Http::response('Test', 500));
    Notification::fake();

    $user = User::factory()->create();

    $serviceMonitorTarget = ServiceMonitoringTarget::factory()
        ->confidential()
        ->hasAttached($user)
        ->hasAttached($user, [], 'confidentialUsers')
        ->create(['is_notified_via_email' => true]);

    (new ServiceMonitoringCheckJob($serviceMonitorTarget))->handle();

    Notification::assertSentTo(
        $user,
        ServiceMonitoringNotification::class,
        fn (ServiceMonitoringNotification $notification) => $notification->toMail($user)->subject === "Aiding App Service Monitoring Alert for {$serviceMonitorTarget->name}"
    );
});

it('builds the database notification for a confidential service monitor without an authenticated user', function () {
    Http::fake(fn () => Http::response('Test', 500));
    Notification::fake();

    $user = User::factory()->create();

    $serviceMonitorTarget = ServiceMonitoringTarget::factory()
        ->confidential()
        ->hasAttached($user)
        ->hasAttached($user, [], 'confidentialUsers')
        ->create(['is_notified_via_database' => true, 'is_notified_via_email' => false]);

    (new ServiceMonitoringCheckJob($serviceMonitorTarget))->handle();

    Notification::assertSentTo(
        $user,
        ServiceMonitoringNotification::class,
        fn (ServiceMonitoringNotification $notification) => str_contains(
            $notification->toDatabase($user)['title'],
            e($serviceMonitorTarget->name),
        )
    );
});

it('still resolves the confidential target after the notification is serialized for the queue', function () {
    $user = User::factory()->create();

    $serviceMonitorTarget = ServiceMonitoringTarget::factory()
        ->confidential()
        ->hasAttached($user, [], 'confidentialUsers')
        ->create();

    $history = $serviceMonitorTarget->histories()->create([
        'response' => 500,
        'response_time' => 0.1,
        'succeeded' => false,
    ]);

    $notification = unserialize(serialize(new ServiceMonitoringNotification($history, MailChannel::class)));

    expect($notification->via($user))->toBe(['mail'])
        ->and($notification->toMail($user)->subject)->toBe("Aiding App Service Monitoring Alert for {$serviceMonitorTarget->name}");
});

it('does not send a notification for a confidential service monitor to a subscriber who is not granted confidential access', function () {
    Http::fake(fn () => Http::response('Test', 500));
    Notification::fake();

    $user = User::factory()->create();

    $serviceMonitorTarget = ServiceMonitoringTarget::factory()
        ->confidential()
        ->hasAttached($user)
        ->create(['is_notified_via_email' => true]);

    (new ServiceMonitoringCheckJob($serviceMonitorTarget))->handle();

    Notification::assertNotSentTo($user, ServiceMonitoringNotification::class);
});

it('does not send a notification for a confidential service monitor to a subscriber whose department is not granted confidential access', function () {
    Http::fake(fn () => Http::response('Test', 500));
    Notification::fake();

    $grantedDepartment = Department::factory()->create();
    $otherDepartment = Department::factory()->create();

    $grantedUser = User::factory()->create();
    $grantedUser->department()->associate($grantedDepartment)->save();

    $ungrantedUser = User::factory()->create();
    $ungrantedUser->department()->associate($otherDepartment)->save();

    $serviceMonitorTarget = ServiceMonitoringTarget::factory()
        ->confidential()
        ->hasAttached($grantedDepartment, [], 'departments')
        ->hasAttached($otherDepartment, [], 'departments')
        ->hasAttached($grantedDepartment, [], 'confidentialDepartments')
        ->create(['is_notified_via_email' => true]);

    (new ServiceMonitoringCheckJob($serviceMonitorTarget))->handle();

    Notification::assertSentTo($grantedUser, ServiceMonitoringNotification::class);
    Notification::assertNotSentTo($ungrantedUser, ServiceMonitoringNotification::class);
});

it('applies basic auth to the availability check request when configured', function () {
    Http::fake(fn () => Http::response('Test', 200));

    $serviceMonitorTarget = ServiceMonitoringTarget::factory()
        ->basicAuth()
        ->create(['monitor_type' => MonitorType::Availability]);

    (new ServiceMonitoringCheckJob($serviceMonitorTarget))->handle();

    Http::assertSent(function (Request $request) use ($serviceMonitorTarget) {
        return $request->hasHeader('Authorization')
            && str_starts_with($request->header('Authorization')[0], 'Basic ')
            && base64_decode(substr($request->header('Authorization')[0], 6)) === "{$serviceMonitorTarget->auth_username}:{$serviceMonitorTarget->auth_password}";
    });
});

it('applies basic auth to the keyword match check request when configured', function () {
    Http::fake(fn () => Http::response('Test 1', 200));

    $serviceMonitorTarget = ServiceMonitoringTarget::factory()
        ->basicAuth()
        ->create([
            'monitor_type' => MonitorType::KeywordMatch,
            'should_contain' => ['Test 1'],
        ]);

    (new ServiceMonitoringCheckJob($serviceMonitorTarget))->handle();

    Http::assertSent(function (Request $request) use ($serviceMonitorTarget) {
        return $request->hasHeader('Authorization')
            && str_starts_with($request->header('Authorization')[0], 'Basic ')
            && base64_decode(substr($request->header('Authorization')[0], 6)) === "{$serviceMonitorTarget->auth_username}:{$serviceMonitorTarget->auth_password}";
    });
});

it('does not apply basic auth when the auth type is none', function () {
    Http::fake(fn () => Http::response('Test', 200));

    $serviceMonitorTarget = ServiceMonitoringTarget::factory()
        ->create(['monitor_type' => MonitorType::Availability]);

    (new ServiceMonitoringCheckJob($serviceMonitorTarget))->handle();

    Http::assertSent(fn (Request $request) => ! $request->hasHeader('Authorization'));
});

it('does not apply basic auth when the feature is inactive', function () {
    ServiceMonitoringAuthTypeFeature::deactivate();

    Http::fake(fn () => Http::response('Test', 200));

    $serviceMonitorTarget = ServiceMonitoringTarget::factory()
        ->basicAuth()
        ->create(['monitor_type' => MonitorType::Availability]);

    (new ServiceMonitoringCheckJob($serviceMonitorTarget))->handle();

    Http::assertSent(fn (Request $request) => ! $request->hasHeader('Authorization'));
});

it('passes when the response status code is in the successful status codes list', function () {
    Http::fake(fn () => Http::response('Test', 200));

    $serviceMonitorTarget = ServiceMonitoringTarget::factory()
        ->apiEndpoint()
        ->create(['successful_status_codes' => [200, 201]]);

    (new ServiceMonitoringCheckJob($serviceMonitorTarget))->handle();

    assertDatabaseHas(HistoricalServiceMonitoring::class, [
        'response' => 200,
        'succeeded' => true,
        'service_monitoring_target_id' => $serviceMonitorTarget->getKey(),
    ]);
});

it('fails when the response status code is not in the successful status codes list', function () {
    Http::fake(fn () => Http::response('Test', 404));

    $serviceMonitorTarget = ServiceMonitoringTarget::factory()
        ->apiEndpoint()
        ->create(['successful_status_codes' => [200]]);

    (new ServiceMonitoringCheckJob($serviceMonitorTarget))->handle();

    assertDatabaseHas(HistoricalServiceMonitoring::class, [
        'response' => 404,
        'succeeded' => false,
        'service_monitoring_target_id' => $serviceMonitorTarget->getKey(),
    ]);

    $history = HistoricalServiceMonitoring::first();

    expect($history->keyword_match_failures)->toBe(['Unexpected status code: 404']);
});

it('does not follow redirects when follow redirection is disabled', function () {
    Http::fake(fn () => Http::response('', 302, ['Location' => 'https://example.com/redirected']));

    $serviceMonitorTarget = ServiceMonitoringTarget::factory()
        ->apiEndpoint()
        ->create([
            'follow_redirection' => false,
            'successful_status_codes' => [302],
        ]);

    (new ServiceMonitoringCheckJob($serviceMonitorTarget))->handle();

    assertDatabaseHas(HistoricalServiceMonitoring::class, [
        'response' => 302,
        'succeeded' => true,
        'service_monitoring_target_id' => $serviceMonitorTarget->getKey(),
    ]);
});

it('fails when the response exceeds the configured maximum latency', function () {
    Http::fake(function () {
        usleep(1000);

        return Http::response('Test', 200);
    });

    $serviceMonitorTarget = ServiceMonitoringTarget::factory()
        ->apiEndpoint()
        ->create([
            'is_max_latency_enabled' => true,
            'max_latency_ms' => -1,
        ]);

    (new ServiceMonitoringCheckJob($serviceMonitorTarget))->handle();

    assertDatabaseHas(HistoricalServiceMonitoring::class, [
        'response' => 200,
        'succeeded' => false,
        'service_monitoring_target_id' => $serviceMonitorTarget->getKey(),
    ]);

    $history = HistoricalServiceMonitoring::first();

    expect($history->keyword_match_failures)->toBe(['Response exceeded maximum allowed latency of -1ms']);
});

it('does not fail for latency when the maximum latency check is disabled', function () {
    Http::fake(fn () => Http::response('Test', 200));

    $serviceMonitorTarget = ServiceMonitoringTarget::factory()
        ->apiEndpoint()
        ->create(['is_max_latency_enabled' => false]);

    (new ServiceMonitoringCheckJob($serviceMonitorTarget))->handle();

    assertDatabaseHas(HistoricalServiceMonitoring::class, [
        'response' => 200,
        'succeeded' => true,
        'service_monitoring_target_id' => $serviceMonitorTarget->getKey(),
    ]);
});

it('converts the response time from seconds to milliseconds before comparing against max_latency_ms', function () {
    // transferStats reports response time in seconds; max_latency_ms is milliseconds. Http::fake()
    // never produces real transfer timings, so this exercises the unit conversion directly rather
    // than through a faked HTTP round trip.
    $serviceMonitorTarget = ServiceMonitoringTarget::factory()
        ->apiEndpoint()
        ->create([
            'is_max_latency_enabled' => true,
            'max_latency_ms' => 1000,
        ]);

    $job = new ServiceMonitoringCheckJob($serviceMonitorTarget);

    $exceedsMaxLatency = (new ReflectionClass($job))->getMethod('exceedsMaxLatency');
    $exceedsMaxLatency->setAccessible(true);

    // 3.33s response against a 1000ms threshold: 3330ms > 1000ms, so this must exceed.
    expect($exceedsMaxLatency->invoke($job, 3.33))->toBeTrue();

    // 0.05s (50ms) response against the same 1000ms threshold must not exceed.
    expect($exceedsMaxLatency->invoke($job, 0.05))->toBeFalse();
});

it('sends the configured request headers', function () {
    Http::fake(fn () => Http::response('Test', 200));

    $serviceMonitorTarget = ServiceMonitoringTarget::factory()
        ->apiEndpoint()
        ->create([
            'request_headers' => [
                ['name' => 'X-Custom-Header', 'value' => 'custom-value'],
            ],
        ]);

    (new ServiceMonitoringCheckJob($serviceMonitorTarget))->handle();

    Http::assertSent(fn (Request $request) => $request->hasHeader('X-Custom-Header', 'custom-value'));
});

it('records a failed check instead of crashing when a stored header name is not valid to send', function () {
    Http::fake(fn () => Http::response('Test', 200));

    // Bypasses the form's regex validation via the factory to simulate a record whose headers
    // ended up invalid some other way (e.g. direct database access), since the form itself now
    // rejects an invalid header name like this before it can be saved.
    $serviceMonitorTarget = ServiceMonitoringTarget::factory()
        ->apiEndpoint()
        ->create([
            'request_headers' => [
                ['name' => 'Invalid Header Name', 'value' => 'value'],
            ],
        ]);

    (new ServiceMonitoringCheckJob($serviceMonitorTarget))->handle();

    $history = HistoricalServiceMonitoring::first();

    expect($history->succeeded)->toBeFalse()
        ->and($history->keyword_match_failures[0])->toContain('Invalid request configuration');
});

it('sends the request body as JSON when configured', function () {
    Http::fake(fn () => Http::response('Test', 200));

    $serviceMonitorTarget = ServiceMonitoringTarget::factory()
        ->apiEndpoint()
        ->create([
            'http_method' => HttpMethod::Post,
            'request_body' => '{"key":"value"}',
            'is_request_body_json' => true,
        ]);

    (new ServiceMonitoringCheckJob($serviceMonitorTarget))->handle();

    Http::assertSent(fn (Request $request) => $request->hasHeader('Content-Type', 'application/json')
        && $request->data() === ['key' => 'value']);
});

it('sends the request body as form-encoded when JSON is not enabled', function () {
    Http::fake(fn () => Http::response('Test', 200));

    $serviceMonitorTarget = ServiceMonitoringTarget::factory()
        ->apiEndpoint()
        ->create([
            'http_method' => HttpMethod::Post,
            'request_body' => 'key=value',
            'is_request_body_json' => false,
        ]);

    (new ServiceMonitoringCheckJob($serviceMonitorTarget))->handle();

    Http::assertSent(fn (Request $request) => str_contains($request->header('Content-Type')[0] ?? '', 'application/x-www-form-urlencoded')
        && $request->body() === 'key=value');
});

it('does not send a request body when the HTTP method does not support one', function () {
    Http::fake(fn () => Http::response('Test', 200));

    $serviceMonitorTarget = ServiceMonitoringTarget::factory()
        ->apiEndpoint()
        ->create([
            'http_method' => HttpMethod::Get,
            'request_body' => 'ignored',
            'is_request_body_json' => false,
        ]);

    (new ServiceMonitoringCheckJob($serviceMonitorTarget))->handle();

    Http::assertSent(fn (Request $request) => blank($request->body()));
});

it('sends the request using the configured HTTP method', function () {
    Http::fake(fn () => Http::response('Test', 200));

    $serviceMonitorTarget = ServiceMonitoringTarget::factory()
        ->apiEndpoint()
        ->create(['http_method' => HttpMethod::Query]);

    (new ServiceMonitoringCheckJob($serviceMonitorTarget))->handle();

    Http::assertSent(fn (Request $request) => $request->method() === 'QUERY');
});
