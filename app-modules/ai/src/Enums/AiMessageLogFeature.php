<?php

namespace AidingApp\Ai\Enums;

use Filament\Support\Contracts\HasLabel;

enum AiMessageLogFeature: string implements HasLabel
{
    case DraftWithAi = 'draft_with_ai';

    case Conversations = 'conversations';

    public function getLabel(): string
    {
        return match ($this) {
            self::DraftWithAi => 'Draft With AI',
            self::Conversations => 'Conversations',
        };
    }

    public static function parse(string | self | null $value): ?self
    {
        if ($value instanceof self) {
            return $value;
        }

        return self::tryFrom($value);
    }
}