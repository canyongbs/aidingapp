<?php

use AidingApp\Contact\Models\Contact;
use AidingApp\IntegrationTwilio\Settings\TwilioSettings;
use AidingApp\IntegrationTwilio\Tests\Fixtures\ClientMock;
use AidingApp\Notification\Enums\SmsMessageEventType;
use AidingApp\Notification\Models\SmsMessage;
use AidingApp\Notification\Tests\Fixtures\TestSmsNotification;
use App\Models\User;
use App\Settings\LicenseSettings;

use function Pest\Laravel\assertDatabaseCount;

use Twilio\Rest\Api\V2010;
use Twilio\Rest\Api\V2010\Account\MessageInstance;
use Twilio\Rest\Api\V2010\Account\MessageList;
use Twilio\Rest\Client;
use Twilio\Rest\MessagingBase;

test('An sms is allowed to be sent if there is available quota and its quota usage is tracked', function () {
    $notifiable = Contact::factory()->create();

    $notification = new TestSmsNotification();

    $settings = app()->make(TwilioSettings::class);

    $settings->account_sid = 'abc123';
    $settings->auth_token = 'abc123';
    $settings->from_number = '+11231231234';

    $settings->save();

    $mockMessageList = mock(MessageList::class);

    $numSegments = rand(1, 5);

    $mockMessageList->shouldReceive('create')->andReturn(
        new MessageInstance(
            new V2010(new MessagingBase(new Client(
                username: $settings->account_sid,
                password: $settings->auth_token,
            ))),
            [
                'sid' => 'abc123',
                'status' => 'queued',
                'from' => '+11231231234',
                'to' => '+11231231234',
                'body' => 'test',
                'num_segments' => $numSegments,
            ],
            'abc123'
        )
    );

    app()->bind(Client::class, fn () => new ClientMock(
        messageList: $mockMessageList,
        username: $settings->account_sid,
        password: $settings->auth_token,
    ));

    $notifiable->notify($notification);

    /** @var SmsMessage $smsMessage */
    $smsMessage = SmsMessage::first();

    expect($smsMessage->quota_usage)
        ->toBe($numSegments)
        ->and($smsMessage->events()->count())
        ->toBe(1)
        ->and($smsMessage->events()->first()->type)
        ->toBe(SmsMessageEventType::Dispatched);
});

test('An sms is prevented from being sent if there is no available quota', function () {
    $notifiable = Contact::factory()->create();

    $notification = new TestSmsNotification();

    $settings = app()->make(TwilioSettings::class);

    $settings->account_sid = 'abc123';
    $settings->auth_token = 'abc123';
    $settings->from_number = '+11231231234';

    $settings->save();

    $licenseSettings = app(LicenseSettings::class);

    $licenseSettings->data->limits->sms = 0;
    $licenseSettings->save();

    $mockMessageList = mock(MessageList::class);

    $numSegments = rand(1, 5);

    $mockMessageList->shouldReceive('create')->andReturn(
        new MessageInstance(
            new V2010(new MessagingBase(new Client(
                username: $settings->account_sid,
                password: $settings->auth_token,
            ))),
            [
                'sid' => 'abc123',
                'status' => 'queued',
                'from' => '+11231231234',
                'to' => '+11231231234',
                'body' => 'test',
                'num_segments' => $numSegments,
            ],
            'abc123'
        )
    );

    app()->bind(Client::class, fn () => new ClientMock(
        messageList: $mockMessageList,
        username: $settings->account_sid,
        password: $settings->auth_token,
    ));

    $notifiable->notify($notification);

    assertDatabaseCount(SmsMessage::class, 1);

    /** @var SmsMessage $smsMessage */
    $smsMessage = SmsMessage::first();

    expect($smsMessage->quota_usage)->toBe(0)
        ->and($smsMessage->events()->count())->toBe(1)
        ->and($smsMessage->events()->first()->type)->toBe(SmsMessageEventType::RateLimited);
});

test('An sms is sent to a user even if there is no available quota', function () {
    $notifiable = User::factory()->create();

    $notification = new TestSmsNotification();

    $settings = app()->make(TwilioSettings::class);

    $settings->account_sid = 'abc123';
    $settings->auth_token = 'abc123';
    $settings->from_number = '+11231231234';

    $settings->save();

    $licenseSettings = app(LicenseSettings::class);

    $licenseSettings->data->limits->sms = 0;
    $licenseSettings->save();

    $mockMessageList = mock(MessageList::class);

    $numSegments = rand(1, 5);

    $mockMessageList->shouldReceive('create')->andReturn(
        new MessageInstance(
            new V2010(new MessagingBase(new Client(
                username: $settings->account_sid,
                password: $settings->auth_token,
            ))),
            [
                'sid' => 'abc123',
                'status' => 'queued',
                'from' => '+11231231234',
                'to' => $notifiable->phone_number,
                'body' => 'test',
                'num_segments' => $numSegments,
            ],
            'abc123'
        )
    );

    app()->bind(Client::class, fn () => new ClientMock(
        messageList: $mockMessageList,
        username: $settings->account_sid,
        password: $settings->auth_token,
    ));

    $notifiable->notify($notification);

    assertDatabaseCount(SmsMessage::class, 1);

    /** @var SmsMessage $smsMessage */
    $smsMessage = SmsMessage::first();

    expect($smsMessage->quota_usage)->toBe(0)
        ->and($smsMessage->events()->count())->toBe(1)
        ->and($smsMessage->events()->first()->type)->toBe(SmsMessageEventType::Dispatched);
})->skip('Currently Users cannot be sent SMS messages, this test will be enabled once the feature is implemented');
