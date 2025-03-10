<?php

namespace AidingApp\Engagement\Enums;

use Filament\Support\Contracts\HasLabel;

enum EngagementResponseType: string implements HasLabel
{
    case Email = 'email';

    public function getLabel(): string
    {
        return $this->name;
    }
}
