<?php

namespace App\Features;

class PortalViewCount extends AbstractFeatureFlag
{
    public function resolve(mixed $scope): mixed
    {
        return false;
    }
}
