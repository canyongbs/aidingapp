<?php

namespace AidingApp\Notification\Enums;

use Filament\Support\Contracts\HasLabel;

enum EmailMessageEventType: string implements HasLabel
{
    // Internal
    case Dispatched = 'dispatched';
    case FailedDispatch = 'failed_dispatch';
    case RateLimited = 'rate_limited';
    case BlockedByDemoMode = 'blocked_by_demo_mode';

    // External
    case Bounce = 'bounce';
    case Complaint = 'complaint';
    case Delivery = 'delivery';
    case Send = 'send';
    case Reject = 'reject';
    case Open = 'open';
    case Click = 'click';
    case RenderingFailure = 'rendering_failure';
    case Subscription = 'subscription';
    case DeliveryDelay = 'delivery_delay';

    public function getLabel(): ?string
    {
        return str($this->name)->headline();
    }
}
