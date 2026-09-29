<?php

namespace AidingApp\KnowledgeBase\Support;

use Illuminate\Support\Str;

final class KnowledgeBasePublicId
{
    public const int LENGTH = 8;

    public static function generate(): string
    {
        return Str::random(self::LENGTH);
    }
}
