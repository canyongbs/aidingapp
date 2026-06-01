<?php

namespace App\Features;

use App\Support\AbstractFeatureFlag;

class EmailAutomaticCreationFeature extends AbstractFeatureFlag
{
    public function resolve(mixed $scope): mixed
    {
        return false;
    }
}
