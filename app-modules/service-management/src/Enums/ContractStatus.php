<?php

namespace AidingApp\ServiceManagement\Enums;

use InvalidArgumentException;
use Filament\Support\Contracts\HasLabel;

enum ContractStatus: string implements HasLabel
{
    case Pending = 'pending';
    case Active = 'active';
    case Expired = 'expired';

    public static function getStatus($startDate, $endDate): self
    {
        $today = now();

        if ($startDate > $today) {
            return self::Pending;
        }

        if ($startDate <= $today && $endDate >= $today) {
            return self::Active;
        }

        if ($endDate < $today) {
            return self::Expired;
        }

        throw new InvalidArgumentException('Invalid date range');
    }

    public function getLabel(): string
    {
        return ucfirst($this->value);
    }
}
