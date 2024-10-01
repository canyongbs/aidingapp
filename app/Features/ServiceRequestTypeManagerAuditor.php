<?php

namespace App\Features;

use App\Support\AbstractFeatureFlag;

class ServiceRequestTypeManagerAuditor extends AbstractFeatureFlag
{
    public function resolve(mixed $scope): mixed
    {
        return false;
    }
}
