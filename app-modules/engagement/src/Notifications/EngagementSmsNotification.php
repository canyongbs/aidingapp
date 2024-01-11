<?php

/*
<COPYRIGHT>

    Copyright © 2022-2023, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

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
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Engagement\Notifications;

use AdvisingApp\Engagement\Models\EngagementDeliverable;
use AdvisingApp\Notification\Models\OutboundDeliverable;
use AdvisingApp\Notification\Notifications\SmsNotification;
use AdvisingApp\Notification\Notifications\BaseNotification;
use AdvisingApp\Notification\Models\Contracts\NotifiableInterface;
use AdvisingApp\Notification\Notifications\Messages\TwilioMessage;
use AdvisingApp\Notification\Notifications\Concerns\SmsChannelTrait;

class EngagementSmsNotification extends BaseNotification implements SmsNotification
{
    use SmsChannelTrait;

    public function __construct(
        public EngagementDeliverable $deliverable
    ) {}

    public function toSms(NotifiableInterface $notifiable): TwilioMessage
    {
        return TwilioMessage::make($notifiable)
            ->content($this->deliverable->engagement->getBody());
    }

    public function beforeSendHook(NotifiableInterface $notifiable, OutboundDeliverable $deliverable, string $channel): void
    {
        $deliverable->update([
            'related_id' => $this->deliverable->id,
            'related_type' => $this->deliverable->getMorphClass(),
        ]);
    }

    public function afterSendHook(NotifiableInterface $notifiable, OutboundDeliverable $deliverable): void
    {
        $this->deliverable->update([
            'external_reference_id' => $deliverable->external_reference_id,
            'external_status' => $deliverable->external_status,
            'delivery_status' => $deliverable->delivery_status,
            'delivered_at' => $deliverable->delivered_at,
            'last_delivery_attempt' => $deliverable->last_delivery_attempt,
            'delivery_response' => $deliverable->delivery_response,
        ]);
    }
}