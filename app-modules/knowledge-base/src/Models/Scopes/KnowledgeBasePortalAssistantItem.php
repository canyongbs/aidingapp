<?php

namespace AidingApp\KnowledgeBase\Models\Scopes;

use AidingApp\KnowledgeBase\Models\KnowledgeBaseItem;
use Illuminate\Database\Eloquent\Builder;

class KnowledgeBasePortalAssistantItem
{
    /**
     * @param Builder<KnowledgeBaseItem> $query
     */
    public function __invoke(Builder $query): void
    {
        $query
            ->where('public', true)
            ->whereNotNull('title')
            ->whereNotNull('article_details_fulltext');
    }
}
