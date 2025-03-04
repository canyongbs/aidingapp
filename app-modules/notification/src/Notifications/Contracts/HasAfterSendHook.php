<?php

namespace AidingApp\Notification\Notifications\Contracts;

use AidingApp\Notification\DataTransferObjects\NotificationResultData;
use AidingApp\Notification\Models\Contracts\CanBeNotified;
use AidingApp\Notification\Models\Contracts\Message;
use Illuminate\Notifications\AnonymousNotifiable;

interface HasAfterSendHook
{
    public function afterSend(AnonymousNotifiable|CanBeNotified $notifiable, Message $message, NotificationResultData $result): void;
}
