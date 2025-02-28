<?php

namespace AidingApp\Engagement\Models\Contracts;

use AidingApp\Engagement\Enums\EngagementDeliveryMethod;
use AidingApp\Notification\Enums\NotificationChannel;

interface HasDeliveryMethod
{
    // public function getDeliveryMethod(): NotificationChannel;
    public function getDeliveryMethod(): EngagementDeliveryMethod;
}
