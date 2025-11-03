<?php

namespace AidingApp\Ai\Support\StreamingChunks;

use Carbon\CarbonInterface;

readonly class Finish
{
    public function __construct(
        public bool $isIncomplete = false,
        public ?string $error = null,
        public ?CarbonInterface $rateLimitResetsAt = null,
    ) {}
}