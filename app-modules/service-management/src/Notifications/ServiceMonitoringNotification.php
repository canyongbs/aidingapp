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

namespace AidingApp\ServiceManagement\Notifications;

use AidingApp\Notification\Notifications\Messages\MailMessage;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ServiceMonitoringNotification extends Notification
{
    use Queueable;

    public function __construct() {}

    /**
     * @return array<int, string>
     */
    public function via(User $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(User $notifiable): MailMessage
    {
        // return MailMessage::make()
        //     ->settings($this->resolveNotificationSetting($notifiable))
        //     ->subject('Alert: Service Check Failure for [TARGET_NAME] ([TARGET_DOMAIN])') //variables
        //     ->greeting('Hello [Responsible_Party_Name],') //variable
        //     ->view()
        //     ->line('This is an automated alert from Aiding App.')
        //     ->line('<strong>Issue Details:</strong>');

        // Service Name: [TARGET_NAME]

        // Domain: [TARGET_DOMAIN]

        // Expected HTTP Status: 200

        // Actual HTTP Status: [RESPONSE_CODE]

        // Response Time: [RESPONSE_TIME] seconds

        // Time of Incident: [CHECK_TIME]

        // Our system detected that the service did not return the expected response during its latest check.
    }

    private function resolveNotificationSetting(User $notifiable): ?NotificationSetting
    {
        return null;
    }
}
