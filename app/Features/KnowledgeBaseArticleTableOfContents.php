<?php

namespace App\Features;

use App\Support\AbstractFeatureFlag;

class KnowledgeBaseArticleTableOfContents extends AbstractFeatureFlag
{
    public function resolve(mixed $scope): mixed
    {
        return false;
    }
}
