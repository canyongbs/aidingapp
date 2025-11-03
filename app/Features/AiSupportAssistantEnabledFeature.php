<?php

namespace App\Features;

use App\Support\AbstractFeatureFlag;

class AiSupportAssistantEnabledFeature extends AbstractFeatureFlag
{
    public function resolve(mixed $scope): mixed
    {
        return false;
    }
}
