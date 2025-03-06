<?php

namespace AidingApp\Notification\Notifications\Channels;

use AidingApp\IntegrationTwilio\Settings\TwilioSettings;
use AidingApp\Notification\DataTransferObjects\SmsChannelResultData;
use AidingApp\Notification\Enums\NotificationChannel;
use AidingApp\Notification\Enums\SmsMessageEventType;
use AidingApp\Notification\Exceptions\NotificationQuotaExceeded;
use AidingApp\Notification\Models\Contracts\CanBeNotified;
use AidingApp\Notification\Models\SmsMessage;
use AidingApp\Notification\Notifications\Contracts\HasAfterSendHook;
use AidingApp\Notification\Notifications\Contracts\HasBeforeSendHook;
use AidingApp\Notification\Notifications\Contracts\OnDemandNotification;
use AidingApp\Notification\Notifications\Messages\TwilioMessage;
use App\Models\User;
use App\Settings\LicenseSettings;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;
use Talkroute\MessageSegmentCalculator\SegmentCalculator;
use Throwable;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Api\V2010;
use Twilio\Rest\Api\V2010\Account\MessageInstance;
use Twilio\Rest\Client;
use Twilio\Rest\MessagingBase;

class SmsChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        [$recipientId, $recipientType] = match (true) {
            $notifiable instanceof Model => [$notifiable->getKey(), $notifiable->getMorphClass()],
            $notifiable instanceof AnonymousNotifiable && $notification instanceof OnDemandNotification => $notification->identifyRecipient(),
            default => [null, 'anonymous'],
        };

        $smsMessage = new SmsMessage([
            'notification_class' => $notification::class,
            'content' => $notification->toSms($notifiable)->toArray(),
            'recipient_id' => $recipientId,
            'recipient_type' => $recipientType,
        ]);

        if ($notification instanceof HasBeforeSendHook) {
            $notification->beforeSend(
                notifiable: $notifiable,
                message: $smsMessage,
                channel: NotificationChannel::Sms
            );
        }

        $smsMessage->save();

        try {
            if ((! ($notifiable instanceof CanBeNotified)) || (! $notifiable->canRecieveSms())) {
                $smsMessage->events()->create([
                    'type' => SmsMessageEventType::FailedDispatch,
                    'payload' => [
                        'error' => 'System determined recipient cannot receive SMS messages.',
                    ],
                    'occurred_at' => now(),
                ]);

                return;
            }

            $message = $notification->toSms($notifiable);

            $twilioSettings = app(TwilioSettings::class);

            $quotaUsage = $this->determineQuotaUsage($message, $smsMessage);

            throw_if($quotaUsage && (! $this->canSendWithinQuotaLimits($quotaUsage)), new NotificationQuotaExceeded());

            if (! $twilioSettings->is_demo_mode_enabled) {
                $client = app(Client::class);

                $messageContent = [
                    'from' => $message->getFrom(),
                    'body' => $message->getContent(),
                ];

                $result = SmsChannelResultData::from([
                    'success' => false,
                ]);

                try {
                    $message = $client->messages->create(
                        config('local_development.twilio.to_number') ?: $message->getRecipientPhoneNumber(),
                        $messageContent
                    );

                    $result->success = true;
                    $result->message = $message;
                } catch (TwilioException $exception) {
                    $result->error = $exception->getMessage();
                }
            } else {
                $result = SmsChannelResultData::from([
                    'success' => true,
                    'message' => new MessageInstance(
                        new V2010(new MessagingBase(new Client(username: 'abc123', password: 'abc123'))),
                        [
                            'sid' => Str::random(),
                            'status' => 'delivered',
                            'from' => $message->getFrom(),
                            'to' => $message->getRecipientPhoneNumber(),
                            'body' => $message->getContent(),
                            'num_segments' => 1,
                        ],
                        'abc123'
                    ),
                ]);
            }

            try {
                if ($result->success) {
                    $smsMessage->quota_usage = $this->determineQuotaUsage($result, $smsMessage);
                    $smsMessage->external_reference_id = $result->message->sid;

                    $smsMessage->events()->create([
                        'type' => $twilioSettings->is_demo_mode_enabled
                            ? SmsMessageEventType::BlockedByDemoMode
                            : SmsMessageEventType::Dispatched,
                        'payload' => $result->message->toArray(),
                        'occurred_at' => now(),
                    ]);

                    $smsMessage->save();
                } else {
                    $smsMessage->events()->create([
                        'type' => SmsMessageEventType::FailedDispatch,
                        'payload' => [
                            'error' => $result->error,
                        ],
                        'occurred_at' => now(),
                    ]);
                }

                if ($notification instanceof HasAfterSendHook) {
                    $notification->afterSend($notifiable, $smsMessage, $result);
                }
            } catch (Throwable $exception) {
                report($exception);
            }
        } catch (NotificationQuotaExceeded $exception) {
            $smsMessage->events()->create([
                'type' => SmsMessageEventType::RateLimited,
                'payload' => [],
                'occurred_at' => now(),
            ]);
        } catch (Throwable $exception) {
            $smsMessage->events()->create([
                'type' => SmsMessageEventType::FailedDispatch,
                'payload' => [],
                'occurred_at' => now(),
            ]);

            throw $exception;
        }
    }

    protected function determineQuotaUsage(TwilioMessage | SmsChannelResultData $message, SmsMessage $smsMessage): int
    {
        if (app(TwilioSettings::class)->is_demo_mode_enabled) {
            return 0;
        }

        if ($smsMessage->recipient instanceof User) {
            return 0;
        }

        if ($message instanceof TwilioMessage) {
            return SegmentCalculator::segmentsCount($message->getContent());
        }

        return $message->message->numSegments;
    }

    protected function canSendWithinQuotaLimits(int $usage): bool
    {
        $licenseSettings = app(LicenseSettings::class);

        $resetWindow = $licenseSettings->data->limits->getResetWindow();

        $currentQuotaUsage = SmsMessage::query()
            ->whereBetween('created_at', [$resetWindow['start'], $resetWindow['end']])
            ->sum('quota_usage');

        return ($currentQuotaUsage + $usage) <= $licenseSettings->data->limits->sms;
    }
}
