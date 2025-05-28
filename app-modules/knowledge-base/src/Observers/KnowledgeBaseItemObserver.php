<?php

namespace AidingApp\KnowledgeBase\Observers;

use AidingApp\KnowledgeBase\Models\KnowledgeBaseItem;
use App\Features\ArticleFullTextSearch;

class KnowledgeBaseItemObserver
{
    public function saving(KnowledgeBaseItem $knowledgeBaseItem): void
    {
        if (ArticleFullTextSearch::active() && ! blank($knowledgeBaseItem->article_details)) {
            $knowledgeBaseItem->article_details_fulltext = strip_tags(tiptap_converter()->asHTML($knowledgeBaseItem->article_details));
        }
    }
}
