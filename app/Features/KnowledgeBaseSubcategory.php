<?php

namespace App\Features;

use App\Support\AbstractFeatureFlag;

class KnowledgeBaseSubcategory extends AbstractFeatureFlag
{
    public function resolve(mixed $scope): mixed
    {
        return false;
    }
}
