<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Aiding App™ is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Aiding App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use AidingApp\IntegrationAwsSesEventHandling\Settings\SesSettings;
use AidingApp\Notification\Exceptions\NotificationQuotaExceeded;
use AidingApp\Notification\Models\OutboundDeliverable;
use App\Models\User;
use App\Settings\LicenseSettings;
use Illuminate\Mail\Events\MessageSent;

use function Pest\Laravel\assertDatabaseCount;

use Tests\Unit\TestEmailNotification;

it('An email is allowed to be sent if there is available quota and it\'s quota usage is tracked', function () {
    Event::fake(MessageSent::class);

    $configurationSet = 'test';

    $settings = app(SesSettings::class);
    $settings->configuration_set = $configurationSet;
    $settings->save();

    $notifiable = User::factory()->create();

    $notification = new TestEmailNotification();

    $notifiable->notify($notification);

    Event::assertDispatched(
        function (MessageSent $event) use ($configurationSet) {
            assertDatabaseCount(OutboundDeliverable::class, 1);

            $outboundDeliverable = OutboundDeliverable::first();

            return $event->message->getHeaders()->get('X-SES-CONFIGURATION-SET')->getBody() === $configurationSet
                && $event->message->getHeaders()->get('X-SES-MESSAGE-TAGS')->getBody() === 'outbound_deliverable_id=' . $outboundDeliverable->getKey()
                && $outboundDeliverable->quota_usage === 1;
        }
    );
});

it('An email is prevented from being sent if there is no available quota', function () {
    Event::fake(MessageSent::class);

    $configurationSet = 'test';

    $settings = app(SesSettings::class);
    $settings->configuration_set = $configurationSet;
    $settings->save();

    $licenseSettings = app(LicenseSettings::class);

    $licenseSettings->data->limits->emails = 0;
    $licenseSettings->save();

    $notifiable = User::factory()->create();

    $notification = new TestEmailNotification();

    expect(fn () => $notifiable->notify($notification))->toThrow(NotificationQuotaExceeded::class);

    Event::assertNotDispatched(MessageSent::class);

    assertDatabaseCount(OutboundDeliverable::class, 0);
});
