<?php

namespace App\Features;

use App\Support\AbstractFeatureFlag;

class PipelineEntryStartDateFeature extends AbstractFeatureFlag
{
    public function resolve(mixed $scope): mixed
    {
        return false;
    }
}
