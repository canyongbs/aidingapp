<?php

use AidingApp\Notification\Enums\EmailMessageEventType;
use AidingApp\Notification\Models\EmailMessage;
use AidingApp\Notification\Notifications\Attributes\SystemNotification;
use AidingApp\Notification\Notifications\Messages\MailMessage;
use AidingApp\Notification\Tests\Fixtures\TestEmailNotification;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

it('will create an EmailMessage for the notification', function () {
    $notifiable = User::factory()->create();

    $notification = new TestEmailNotification();

    $notifiable->notify($notification);

    $emailMessages = EmailMessage::all();

    expect($emailMessages->count())->toBe(1);
    expect($emailMessages->first()->notification_class)->toBe(TestEmailNotification::class);
});

it('will not send emails in demo mode and record a BlockedByDemoMode event', function () {
    $user = User::factory()->create();

    $tenantConfig = Tenant::current()->config;
    $tenantConfig->mail->isDemoModeEnabled = true;
    Tenant::current()->update([
        'config' => $tenantConfig,
    ]);

    $notification = new TestEmailNotification();
    $user->notify($notification);

    $emailMessages = EmailMessage::query()
        ->with('events')
        ->get();

    expect($emailMessages->count())->toBe(1);
    expect($emailMessages->first()->events->count())->toBe(1);
    expect($emailMessages->first()->events->first()->type)->toBe(EmailMessageEventType::BlockedByDemoMode);
});

it('will send system notifications in demo mode', function () {
    $user = User::factory()->create();

    $tenantConfig = Tenant::current()->config;
    $tenantConfig->mail->isDemoModeEnabled = true;
    $tenantConfig->mail->isExcludingSystemNotificationsFromDemoMode = true;
    Tenant::current()->update([
        'config' => $tenantConfig,
    ]);

    $notification = new TestSystemNotification();
    $user->notify($notification);

    $emailMessages = EmailMessage::query()
        ->with('events')
        ->get();

    expect($emailMessages->count())->toBe(1);
    expect($emailMessages->first()->events->count())->toBe(1);
    expect($emailMessages->first()->events->first()->type)->toBe(EmailMessageEventType::Dispatched);
});

it('will not send system notifications in demo mode when system notifications are not excluded', function () {
    $user = User::factory()->create();

    $tenantConfig = Tenant::current()->config;
    $tenantConfig->mail->isDemoModeEnabled = true;
    $tenantConfig->mail->isExcludingSystemNotificationsFromDemoMode = false;
    Tenant::current()->update([
        'config' => $tenantConfig,
    ]);

    $notification = new TestSystemNotification();
    $user->notify($notification);

    $emailMessages = EmailMessage::query()
        ->with('events')
        ->get();

    expect($emailMessages->count())->toBe(1);
    expect($emailMessages->first()->events->count())->toBe(1);
    expect($emailMessages->first()->events->first()->type)->toBe(EmailMessageEventType::BlockedByDemoMode);
});

#[SystemNotification]
class TestSystemNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return MailMessage::make()
            ->subject('Test Subject')
            ->greeting('Test Greeting')
            ->content('This is a test email');
    }
}
