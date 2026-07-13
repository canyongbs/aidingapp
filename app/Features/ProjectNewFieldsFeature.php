<?php

namespace App\Features;

use App\Support\AbstractFeatureFlag;

class ProjectNewFieldsFeature extends AbstractFeatureFlag
{
    public function resolve(mixed $scope): mixed
    {
        return false;
    }
}
