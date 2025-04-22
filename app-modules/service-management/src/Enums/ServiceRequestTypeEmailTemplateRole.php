<?php

namespace AidingApp\ServiceManagement\Enums;

use Filament\Support\Contracts\HasLabel;

enum ServiceRequestTypeEmailTemplateRole: string implements HasLabel
{
    case Manager = 'manager';

    case Auditor = 'auditor';

    case Customer = 'customer';

    public function getLabel(): string
    {
        return match ($this) {
            self::Manager => 'Manager',
            self::Auditor => 'Auditor',
            self::Customer => 'Customer',
        };
    }
}
