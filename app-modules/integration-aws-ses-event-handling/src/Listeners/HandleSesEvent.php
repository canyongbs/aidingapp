<?php

namespace AidingApp\IntegrationAwsSesEventHandling\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use AidingApp\Notification\Models\OutboundDeliverable;
use AidingApp\IntegrationAwsSesEventHandling\Events\SesEvent;
use AidingApp\Notification\Actions\UpdateOutboundDeliverableEmailStatus;
use AidingApp\Notification\Events\CouldNotFindOutboundDeliverableFromExternalReference;

abstract class HandleSesEvent implements ShouldQueue
{
    public function handle(SesEvent $event): void
    {
        $outboundDeliverable = OutboundDeliverable::query()
            ->where('id', data_get($event->data->mail->tags, 'outbound_deliverable_id'))
            ->first();

        if (is_null($outboundDeliverable)) {
            CouldNotFindOutboundDeliverableFromExternalReference::dispatch($event->data);

            return;
        }

        UpdateOutboundDeliverableEmailStatus::dispatch($outboundDeliverable, $event->data);
    }
}
