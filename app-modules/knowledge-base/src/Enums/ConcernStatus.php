<?php

namespace AidingApp\KnowledgeBase\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum ConcernStatus: string implements HasLabel
{
    case New = 'new';

    case Resolved = 'resolved';

    case Archived = 'archived';

    public function getLabel(): string|Htmlable|null
    {
        return $this->name;
    }
}
