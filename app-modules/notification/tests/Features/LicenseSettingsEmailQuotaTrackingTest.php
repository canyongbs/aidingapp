<?php

use AidingApp\Contact\Models\Contact;
use AidingApp\IntegrationAwsSesEventHandling\Settings\SesSettings;
use AidingApp\Notification\Enums\EmailMessageEventType;
use AidingApp\Notification\Models\EmailMessage;
use AidingApp\Notification\Tests\Fixtures\TestEmailNotification;
use App\Models\Tenant;
use App\Models\User;
use App\Settings\LicenseSettings;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Event;

use function Pest\Laravel\assertDatabaseCount;

test('An email is allowed to be sent if there is available quota and its quota usage is tracked', function () {
    Event::fake(MessageSent::class);

    $configurationSet = 'test';

    $settings = app(SesSettings::class);
    $settings->configuration_set = $configurationSet;
    $settings->save();

    $notifiable = Contact::factory()->create();

    $notification = new TestEmailNotification();

    $notifiable->notify($notification);

    Event::assertDispatched(
        function (MessageSent $event) use ($configurationSet) {
            assertDatabaseCount(EmailMessage::class, 1);

            $emailMessage = EmailMessage::first();

            return $event->message->getHeaders()->get('X-SES-CONFIGURATION-SET')->getBody() === $configurationSet
                && $event->message->getHeaders()->get('X-SES-MESSAGE-TAGS')->getBody() === sprintf('app_message_id=%s, tenant_id=%s', EmailMessage::first()->getKey(), Tenant::current()->getKey())
                && $emailMessage->quota_usage === 1;
        }
    );
});

test('An email is prevented from being sent if there is no available quota', function () {
    Event::fake(MessageSent::class);

    $configurationSet = 'test';

    $settings = app(SesSettings::class);
    $settings->configuration_set = $configurationSet;
    $settings->save();

    $licenseSettings = app(LicenseSettings::class);

    $licenseSettings->data->limits->emails = 0;
    $licenseSettings->save();

    $notifiable = Contact::factory()->create();

    $notification = new TestEmailNotification();

    $notifiable->notify($notification);

    Event::assertNotDispatched(MessageSent::class);

    assertDatabaseCount(EmailMessage::class, 1);

    /** @var EmailMessage $emailMessage */
    $emailMessage = EmailMessage::first();

    expect($emailMessage->quota_usage)->toBe(0)
        ->and($emailMessage->events->first()->type)->toBe(EmailMessageEventType::RateLimited);
});

test('An email is sent to a user even if there is no available quota', function () {
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

    $notifiable->notify($notification);

    Event::assertDispatched(MessageSent::class);

    assertDatabaseCount(EmailMessage::class, 1);
});
