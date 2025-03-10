<?php

namespace AidingApp\Engagement\DataTransferObjects;

use AidingApp\Notification\Enums\NotificationChannel;
use AidingApp\Notification\Models\Contracts\CanBeNotified;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Collection;
use Spatie\LaravelData\Data;

class EngagementCreationData extends Data
{
    public function __construct(
        public User $user,
        public CanBeNotified | Collection $recipient,
        public NotificationChannel $channel,
        public ?string $subject = null,
        public ?array $body = null,
        public array $temporaryBodyImages = [],
        public ?CarbonInterface $scheduledAt = null,
    ) {}
}
