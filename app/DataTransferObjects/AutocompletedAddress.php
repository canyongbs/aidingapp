<?php

namespace App\DataTransferObjects;

use Spatie\LaravelData\Data;

class AutocompletedAddress extends Data
{
    public function __construct(
        public string $line1,
        public string $city,
        public string $state,
        public string $postalCode,
        public string $country,
        public string $label,
    ) {}
}