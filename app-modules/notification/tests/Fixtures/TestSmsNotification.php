<?php

namespace AidingApp\Notification\Tests\Fixtures;

use AidingApp\Notification\Notifications\Messages\TwilioMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TestSmsNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['sms'];
    }

    public function toSms(object $notifiable): TwilioMessage
    {
        return TwilioMessage::make($notifiable)
            ->content('This is a test');
    }
}
