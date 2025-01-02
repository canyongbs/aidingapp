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

namespace AidingApp\Notification\Notifications\Channels;

use AidingApp\Notification\DataTransferObjects\DatabaseChannelResultData;
use AidingApp\Notification\DataTransferObjects\NotificationResultData;
use AidingApp\Notification\Models\OutboundDeliverable;
use AidingApp\Notification\Notifications\BaseNotification;
use Illuminate\Notifications\Channels\DatabaseChannel as BaseDatabaseChannel;
use Illuminate\Notifications\Notification;

class DatabaseChannel extends BaseDatabaseChannel
{
    public function send($notifiable, Notification $notification): void
    {
        /** @var BaseNotification $notification */
        $deliverable = $notification->beforeSend($notifiable, DatabaseChannel::class);

        if ($deliverable === false) {
            // Do anything else we need to notify sending party that notification was not sent
            return;
        }

        $result = $this->handle($notifiable, $notification);

        $notification->afterSend($notifiable, $deliverable, $result);
    }

    public function handle(object $notifiable, BaseNotification $notification): NotificationResultData
    {
        parent::send($notifiable, $notification);

        return new DatabaseChannelResultData(
            success: true,
        );
    }

    public static function afterSending(object $notifiable, OutboundDeliverable $deliverable, DatabaseChannelResultData $result): void
    {
        if ($result->success) {
            $deliverable->markDeliverySuccessful();
        } else {
            $deliverable->markDeliveryFailed('Failed to send notification');
        }
    }
}
