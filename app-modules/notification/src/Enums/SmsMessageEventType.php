<?php

namespace AidingApp\Notification\Enums;

use Filament\Support\Contracts\HasLabel;

enum SmsMessageEventType: string implements HasLabel
{
    // Internal
    case Dispatched = 'dispatched';
    case FailedDispatch = 'failed_dispatch';
    case RateLimited = 'rate_limited';
    case BlockedByDemoMode = 'blocked_by_demo_mode';

    // External
    case Queued = 'queued';
    case Canceled = 'canceled';
    case Sent = 'sent';
    case Failed = 'failed';
    case Delivered = 'delivered';
    case Undelivered = 'undelivered';
    case Read = 'read';

    public function getLabel(): ?string
    {
        return $this->name;
    }
}
