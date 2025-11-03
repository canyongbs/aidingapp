<?php

namespace AidingApp\Ai\Enums;

use Filament\Support\Contracts\HasLabel;

enum AiReasoningEffort: string implements HasLabel
{
    case Minimal = 'minimal';

    case Low = 'low';

    case Medium = 'medium';

    case High = 'high';

    public function getLabel(): string
    {
        return $this->name;
    }

    public static function parse(string | self | null $value): ?self
    {
        if ($value instanceof self) {
            return $value;
        }

        return self::tryFrom($value);
    }
}