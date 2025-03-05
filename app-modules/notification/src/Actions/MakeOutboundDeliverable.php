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

namespace AidingApp\Notification\Actions;

use AidingApp\Notification\Enums\NotificationChannel;
use AidingApp\Notification\Models\OutboundDeliverable;
use AidingApp\Notification\Notifications\Channels\DatabaseChannel;
use AidingApp\Notification\Notifications\Channels\EmailChannel;
use AidingApp\Notification\Notifications\Channels\SmsChannel;
use AidingApp\Notification\Notifications\Contracts\OnDemandNotification;
use AidingApp\Notification\Notifications\DatabaseNotification;
use AidingApp\Notification\Notifications\EmailNotification;
use AidingApp\Notification\Notifications\SmsNotification;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Notification;

class MakeOutboundDeliverable
{
    public function handle(Notification $notification, object $notifiable, string $channel): OutboundDeliverable
    {
        $channel = match ($channel) {
            SmsChannel::class => NotificationChannel::Sms,
            EmailChannel::class => NotificationChannel::Email,
            DatabaseChannel::class => NotificationChannel::Database,
        };

        $content = match (true) {
            $channel == NotificationChannel::Sms && $notification instanceof SmsNotification => $notification->toSms($notifiable)->toArray(),
            $channel == NotificationChannel::Email && $notification instanceof EmailNotification => $notification->toMail($notifiable)->toArray(),
            $channel == NotificationChannel::Database && $notification instanceof DatabaseNotification => $notification->toDatabase($notifiable),
        };

        $recipientId = null;
        $recipientType = 'anonymous';

        if ($notifiable instanceof AnonymousNotifiable && $notification instanceof OnDemandNotification) {
            [$recipientId, $recipientType] = $notification->identifyRecipient();
        }

        return new OutboundDeliverable([
            'channel' => $channel,
            'notification_class' => get_class($notification),
            'content' => $content,
            'recipient_id' => ! $notifiable instanceof AnonymousNotifiable ? $notifiable->getKey() : $recipientId,
            'recipient_type' => ! $notifiable instanceof AnonymousNotifiable ? $notifiable->getMorphClass() : $recipientType,
        ]);
    }
}
