<?php

namespace AidingApp\Portal\Enums;

use Filament\Support\Contracts\HasLabel;

enum GdprDeclineOptions: string implements HasLabel
{
    case Decline = 'Decline';

    case Cancel = 'Cancel';

    public function getLabel(): string
    {
        return $this->value;
    }
}
