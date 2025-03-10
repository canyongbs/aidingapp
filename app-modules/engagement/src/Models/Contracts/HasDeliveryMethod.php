<?php

namespace AidingApp\Engagement\Models\Contracts;

use AidingApp\Notification\Enums\NotificationChannel;

interface HasDeliveryMethod
{
    public function getDeliveryMethod(): NotificationChannel;
}
