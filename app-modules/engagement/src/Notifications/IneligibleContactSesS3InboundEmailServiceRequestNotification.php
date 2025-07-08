<?php

namespace AidingApp\Engagement\Notifications;

use AidingApp\Notification\Notifications\Messages\MailMessage;
use AidingApp\ServiceManagement\Models\TenantServiceRequestTypeDomain;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class IneligibleContactSesS3InboundEmailServiceRequestNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected TenantServiceRequestTypeDomain $serviceRequestTypeDomain,
        protected string $content,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(AnonymousNotifiable $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(AnonymousNotifiable $notifiable): MailMessage
    {
        return MailMessage::make()
            ->subject('Ineligible for Service Request Creation')
            ->line('Thank you for your service request.')
            ->line('Unfortunately, we were unable to locate your serviceable account in our systems and therefore are unable to automatically open your service request.')
            ->line('Please contact your account manager for additional details.')
            ->line('Original Message (Inline embeds like images may not be displayed):')
            ->line(new HtmlString('<hr />'))
            ->line(str($this->content)->sanitizeHtml()->toHtmlString())
            ->line(new HtmlString('<br /><hr />'));
    }
}
