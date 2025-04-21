<?php

namespace AidingApp\ServiceManagement\Enums;

use Filament\Support\Contracts\HasLabel;

enum ServiceRequestTypeEmailTemplateRole: string implements HasLabel
{
    case Customer = 'customer';

    case Manager = 'manager';

    case Auditor = 'auditor';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Customer => 'Customer',
            self::Manager => 'Manager',
            self::Auditor => 'Auditor',
        };
    }
}
