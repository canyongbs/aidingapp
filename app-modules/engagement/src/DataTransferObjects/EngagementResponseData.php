<?php

namespace AidingApp\Engagement\DataTransferObjects;

use Spatie\LaravelData\Data;

class EngagementResponseData extends Data
{
    public function __construct(
        public string $from,
        public string $body,
    ) {}
}
