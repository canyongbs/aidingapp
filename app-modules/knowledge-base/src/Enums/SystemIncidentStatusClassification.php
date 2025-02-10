<?php

namespace AidingApp\KnowledgeBase\Enums;

use Filament\Support\Contracts\HasLabel;

enum SystemIncidentStatusClassification: string implements HasLabel
{
    case Open = 'open';

    case Resolved = 'resolved';

    public function getLabel(): ?string
    {
        return match ($this) {
            SystemIncidentStatusClassification::Open => 'Open',
            SystemIncidentStatusClassification::Resolved => 'Resolved',
            default => $this->name,
        };
    }
}
