<?php

namespace AidingApp\Ai\Support\StreamingChunks;

readonly class Image
{
    public function __construct(
        public string $content,
        public string $format,
    ) {}
}