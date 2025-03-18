<?php

namespace AidingApp\ServiceManagement\Enums;

use Filament\Support\Contracts\HasLabel;

enum ServiceMonitoringFrequency: string implements HasLabel
{
    case OneHour = '1 hour';

    case TwentyFourHours = '24 hours';

    public function getLabel(): string
    {
        return $this->value;
    }
}
