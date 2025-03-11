<?php

namespace AidingApp\IntegrationAwsSesEventHandling\Listeners;

use AidingApp\IntegrationAwsSesEventHandling\DataTransferObjects\SesEventData;
use AidingApp\IntegrationAwsSesEventHandling\Events\SesEvent;
use AidingApp\Notification\Models\EmailMessage;
use Illuminate\Contracts\Queue\ShouldQueue;

abstract class HandleSesEvent implements ShouldQueue
{
    abstract public function handle(SesEvent $event): void;

    protected function getEmailMessageFromData(SesEventData $data): ?EmailMessage
    {
        return EmailMessage::query()
            ->where('id', data_get($data->mail->tags, 'app_message_id'))
            ->first();
    }
}
