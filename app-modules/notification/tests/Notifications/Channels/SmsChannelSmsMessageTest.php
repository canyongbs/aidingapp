<?php

use AidingApp\Contact\Models\Contact;
use AidingApp\IntegrationTwilio\Settings\TwilioSettings;
use AidingApp\IntegrationTwilio\Tests\Fixtures\ClientMock;
use AidingApp\Notification\Enums\SmsMessageEventType;
use AidingApp\Notification\Models\SmsMessage;
use AidingApp\Notification\Tests\Fixtures\TestSmsNotification;
use Twilio\Rest\Api\V2010;
use Twilio\Rest\Api\V2010\Account\MessageInstance;
use Twilio\Rest\Api\V2010\Account\MessageList;
use Twilio\Rest\Client;
use Twilio\Rest\MessagingBase;

beforeEach(function () {
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
});

it('will create an SmsMessage for the notification', function () {
    $notifiable = Contact::factory()->create();

    $notification = new TestSmsNotification();

    $notifiable->notify($notification);

    $smsMessages = SmsMessage::all();

    expect($smsMessages->count())->toBe(1);
    expect($smsMessages->first()->notification_class)->toBe(TestSmsNotification::class);
    expect($smsMessages->first()->events->first()->type)->toBe(SmsMessageEventType::Dispatched);
});

// TODO Add more tests for SMS Demo mode etc.
