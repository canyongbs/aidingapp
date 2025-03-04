<?php

namespace AidingApp\Notification\Notifications\Contracts;

use AidingApp\Notification\Enums\NotificationChannel;
use AidingApp\Notification\Models\Contracts\CanBeNotified;
use AidingApp\Notification\Models\Contracts\Message;
use Illuminate\Notifications\AnonymousNotifiable;

interface HasBeforeSendHook
{
    public function beforeSend(AnonymousNotifiable|CanBeNotified $notifiable, Message $message, NotificationChannel $channel): void;
}
