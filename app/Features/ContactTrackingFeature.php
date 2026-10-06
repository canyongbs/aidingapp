<?php

namespace App\Features;

use App\Support\AbstractFeatureFlag;

class ContactTrackingFeature extends AbstractFeatureFlag
{
    public function resolve(mixed $scope): mixed
    {
        return false;
    }
}
