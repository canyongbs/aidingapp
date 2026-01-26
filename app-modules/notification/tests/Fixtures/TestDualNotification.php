<?php

namespace AidingApp\Notification\Tests\Fixtures;

use AidingApp\Notification\Notifications\Messages\MailMessage;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TestDualNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return MailMessage::make()
            ->subject('Test Subject')
            ->greeting('Test Greeting')
            ->content('This is a test email');
    }

    public function toDatabase(object $notifiable): array
    {
        return FilamentNotification::make()
            ->success()
            ->title('Test Title')
            ->body('This is a test.')
            ->getDatabaseMessage();
    }
}
