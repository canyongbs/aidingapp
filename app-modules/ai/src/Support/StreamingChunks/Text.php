<?php

namespace AidingApp\Ai\Support\StreamingChunks;

readonly class Text
{
    public function __construct(
        public string $content,
    ) {}
}
