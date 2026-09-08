<?php

namespace App\Features;

use App\Support\AbstractFeatureFlag;

class GroupManagementFeature extends AbstractFeatureFlag
{
    public function resolve(mixed $scope): mixed
    {
        return false;
    }
}
