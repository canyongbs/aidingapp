<?php

namespace AidingApp\ServiceManagement\Enums;

use Filament\Support\Contracts\HasLabel;

enum ServiceMonitoringReportFrequency: string implements HasLabel
{
    case Daily = 'daily';

    case Weekly = 'weekly';

    case Monthly = 'monthly';

    public function getLabel(): string
    {
        return $this->name;
    }
}
