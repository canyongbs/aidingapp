<?php

namespace AidingApp\IntegrationAwsSesEventHandling\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use AidingApp\IntegrationAwsSesEventHandling\DataTransferObjects\SesEventData;

abstract class SesEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public SesEventData $data,
    ) {}
}
