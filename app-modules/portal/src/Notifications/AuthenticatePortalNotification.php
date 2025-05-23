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

namespace AidingApp\Portal\Notifications;

use AidingApp\Notification\Enums\NotificationChannel;
use AidingApp\Notification\Models\StoredAnonymousNotifiable;
use AidingApp\Notification\Notifications\Attributes\SystemNotification;
use AidingApp\Notification\Notifications\Contracts\OnDemandNotification;
use AidingApp\Notification\Notifications\Messages\MailMessage;
use AidingApp\Portal\Models\PortalAuthentication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

#[SystemNotification]
class AuthenticatePortalNotification extends Notification implements ShouldQueue, OnDemandNotification
{
    use Queueable;

    public function __construct(
        public PortalAuthentication $authentication,
        public int $code,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return MailMessage::make()
            ->subject(__('Your authentication code for :appname', ['appname' => config('app.name')]))
            ->line("Your code is: {$this->code}.")
            ->line('You should type this code into the portal to authenticate yourself.')
            ->line('For security reasons, the code will expire in 24 hours, but you can always request another.');
    }

    public function identifyRecipient(?object $notifiable = null): array
    {
        /** @var ?Contact $contact */
        $contact = $this->authentication->educatable;

        if (! $contact) {
            return [
                StoredAnonymousNotifiable::query()->createOrFirst([
                    'type' => NotificationChannel::Email,
                    'route' => $notifiable->routeNotificationFor('mail', $this),
                ])->getKey(),
                (new StoredAnonymousNotifiable())->getMorphClass(),
            ];
        }

        return [
            $contact->getKey(),
            $this->authentication->educatable->getMorphClass(),
        ];
    }
}
