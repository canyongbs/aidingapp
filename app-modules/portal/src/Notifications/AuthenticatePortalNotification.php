<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

namespace AidingApp\Portal\Notifications;

use AidingApp\Portal\Models\PortalAuthentication;
use AidingApp\Notification\Notifications\BaseNotification;
use AidingApp\Notification\Notifications\EmailNotification;
use AidingApp\Notification\Notifications\Messages\MailMessage;
use AidingApp\Notification\Notifications\OnDemandNotification;
use AidingApp\Notification\Notifications\Concerns\EmailChannelTrait;

class AuthenticatePortalNotification extends BaseNotification implements EmailNotification, OnDemandNotification
{
    use EmailChannelTrait;

    public function __construct(
        public PortalAuthentication $authentication,
        public int $code,
    ) {}

    public function toEmail(object $notifiable): MailMessage
    {
        return MailMessage::make()
            ->subject("Your authentication code for {$this->authentication->portal_type->getLabel()}")
            ->line("Your code is: {$this->code}.")
            ->line('You should type this code into the portal to authenticate yourself.')
            ->line('For security reasons, the code will expire in 24 hours, but you can always request another.');
    }

    public function identifyRecipient(): array
    {
        return [
            $this->authentication->educatable->getKey(),
            $this->authentication->educatable->getMorphClass(),
        ];
    }
}
