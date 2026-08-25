<?php

namespace AidingApp\ServiceManagement\Enums;

use Filament\Support\Contracts\HasLabel;

enum MonitorType: string implements HasLabel
{
    case Availability = 'availability';

    case KeywordMatch = 'keyword_match';

    public function getLabel(): string
    {
        return match ($this) {
            self::Availability => 'Availability',
            self::KeywordMatch => 'Keyword Match',
        };
    }
}
