<?php

namespace AidingApp\ServiceManagement\Enums;

use Filament\Support\Contracts\HasLabel;

enum ServiceMonitoringFrequency: string implements HasLabel
{
    case OneHour = '1_hour';

    case TwentyFourHours = '24_hours';

    public function getLabel(): string
    {
        return str_replace('_', ' ', $this->value);
    }
}
