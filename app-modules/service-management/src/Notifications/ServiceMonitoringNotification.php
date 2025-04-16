<?php

namespace AidingApp\ServiceManagement\Notifications;

use AidingApp\Notification\Notifications\Messages\MailMessage;
use App\Models\EmailTemplate;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ServiceMonitoringNotification extends Notification
{
    use Queueable;

    public function __construct()
    {
        //
    }

    /**
     * @return array<int, string>
     */
    public function via(User $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(User $notifiable): MailMessage
    {
        return MailMessage::make()
            ->settings($this->resolveNotificationSetting($notifiable))
            ->subject("Alert: Service Check Failure for [TARGET_NAME] ([TARGET_DOMAIN])") //variables
            ->greeting("Hello [Responsible_Party_Name],") //variable
            ->line('This is an automated alert from Aiding App.')
            ->line("<strong>Issue Details:</strong>");

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
