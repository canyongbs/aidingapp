<?php

namespace App\Features;

use App\Support\AbstractFeatureFlag;

class ServiceRequestStatusSystemProtection extends AbstractFeatureFlag
{
    public function resolve(mixed $scope): mixed
    {
        return false;
    }
}
