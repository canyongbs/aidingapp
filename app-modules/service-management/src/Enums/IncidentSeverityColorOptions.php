<?php

namespace AidingApp\ServiceManagement\Enums;

use Filament\Support\Contracts\HasLabel;

enum IncidentSeverityColorOptions: string implements HasLabel
{
    case Success = 'success';

    case Danger = 'danger';

    case Warning = 'warning';

    case Info = 'info';

    case Primary = 'primary';

    case Gray = 'gray';

    public function getLabel(): string
    {
        return $this->value;
    }
}
