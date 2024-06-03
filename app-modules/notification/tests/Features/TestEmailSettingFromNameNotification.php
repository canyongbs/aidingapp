<?php

namespace AidingApp\Notification\Tests\Features;

use App\Models\NotificationSetting;
use AidingApp\Notification\Notifications\BaseNotification;
use AidingApp\Notification\Notifications\EmailNotification;
use AidingApp\Notification\Notifications\Messages\MailMessage;
use AidingApp\Notification\Notifications\Concerns\EmailChannelTrait;

class TestEmailSettingFromNameNotification extends BaseNotification implements EmailNotification
{
    use EmailChannelTrait;

    public function __construct(
        public NotificationSetting $setting,
    ) {}

    public function toEmail(object $notifiable): MailMessage
    {
        return MailMessage::make()
            ->settings($this->setting)
            ->subject('Test Subject')
            ->greeting('Test Greeting')
            ->content('This is a test email')
            ->salutation('Test Salutation');
    }
}
