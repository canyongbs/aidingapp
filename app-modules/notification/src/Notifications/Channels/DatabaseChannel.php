<?php

namespace AidingApp\Notification\Notifications\Channels;

use AidingApp\Notification\DataTransferObjects\DatabaseChannelResultData;
use AidingApp\Notification\Enums\NotificationChannel;
use AidingApp\Notification\Models\DatabaseMessage;
use AidingApp\Notification\Notifications\Contracts\HasAfterSendHook;
use AidingApp\Notification\Notifications\Contracts\HasBeforeSendHook;
use AidingApp\Notification\Notifications\Contracts\OnDemandNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Channels\DatabaseChannel as BaseDatabaseChannel;
use Illuminate\Notifications\Notification;
use Throwable;

class DatabaseChannel extends BaseDatabaseChannel
{
    public function send($notifiable, Notification $notification): void
    {
        [$recipientId, $recipientType] = match (true) {
            $notifiable instanceof Model => [$notifiable->getKey(), $notifiable->getMorphClass()],
            $notifiable instanceof AnonymousNotifiable && $notification instanceof OnDemandNotification => $notification->identifyRecipient(),
            default => [null, 'anonymous'],
        };

        $databaseMessage = new DatabaseMessage([
            'notification_class' => $notification::class,
            'content' => $notification->toDatabase($notifiable),
            'recipient_id' => $recipientId,
            'recipient_type' => $recipientType,
        ]);

        if ($notification instanceof HasBeforeSendHook) {
            $notification->beforeSend(
                notifiable: $notifiable,
                message: $databaseMessage,
                channel: NotificationChannel::Database
            );
        }

        try {
            $notificationModel = parent::send($notifiable, $notification);

            $result = new DatabaseChannelResultData(
                success: true,
            );

            try {
                $databaseMessage->notification_id = $notificationModel->getKey();

                $databaseMessage->save();

                if ($notification instanceof HasAfterSendHook) {
                    $notification->afterSend($notifiable, $databaseMessage, $result);
                }
            } catch (Throwable $exception) {
                report($exception);
            }
        } catch (Throwable $exception) {
            throw $exception;
        }
    }
}
