<?php

namespace AidingApp\KnowledgeBase\Observers;

use AidingApp\KnowledgeBase\Models\KnowledgeBaseItem;

class KnowledgeBaseItemObserver
{
    public function saving(KnowledgeBaseItem $knowledgeBaseItem): void
    {
        if(!blank($knowledgeBaseItem->article_details)){
            $articleDetails = tiptap_converter()->asHTML($knowledgeBaseItem->article_details);
        }
        $knowledgeBaseItem->article_details_fulltext = isset($articleDetails) ? strip_tags($articleDetails) : null;
    }
}
