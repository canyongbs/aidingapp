<?php

namespace AidingApp\IntegrationAwsSesEventHandling\Listeners;

use AidingApp\IntegrationAwsSesEventHandling\Events\SesEvent;
use AidingApp\IntegrationAwsSesEventHandling\Exceptions\CouldNotFindEmailMessageFromData;
use AidingApp\Notification\Enums\EmailMessageEventType;

class HandleSesRenderingFailureEvent extends HandleSesEvent
{
    public function handle(SesEvent $event): void
    {
        $emailMessage = $this->getEmailMessageFromData($event->data);

        if (is_null($emailMessage)) {
            report(new CouldNotFindEmailMessageFromData($event->data));

            return;
        }

        $emailMessage->events()->create([
            'type' => EmailMessageEventType::RenderingFailure,
            'payload' => $event->data->toArray(),
            'occurred_at' => now(),
        ]);
    }
}
