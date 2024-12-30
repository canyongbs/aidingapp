<?php

namespace AidingApp\Authorization\Enums;

use Filament\Support\Contracts\HasLabel;

enum AzureMatchingProperty: string implements HasLabel
{
    case UserPrincipalName = 'user_principal_name';

    case Mail = 'mail';

    public function getLabel(): ?string
    {
        return $this->name;
    }
}
