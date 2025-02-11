<?php

namespace AidingApp\ServiceManagement\Enums;

use Filament\Support\Contracts\HasLabel;

enum SystemIncidentStatusClassification: string implements HasLabel
{
    case Open = 'open';

    case Resolved = 'resolved';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Open => 'Open',
            self::Resolved => 'Resolved',
            default => $this->name,
        };
    }
}
