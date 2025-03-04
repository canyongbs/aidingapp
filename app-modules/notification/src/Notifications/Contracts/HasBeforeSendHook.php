<?php

namespace AidingApp\Notification\Notifications\Contracts;

use AidingApp\Notification\Enums\NotificationChannel;
use AidingApp\Notification\Models\Contracts\CanBeNotified;
use AidingApp\Notification\Models\Contracts\Message;
use AidingApp\Notification\Models\OutboundDeliverable;
use Illuminate\Notifications\AnonymousNotifiable;

interface HasBeforeSendHook
{
    public function beforeSend(AnonymousNotifiable|CanBeNotified $notifiable, OutboundDeliverable|Message $message, NotificationChannel $channel): void;
}
