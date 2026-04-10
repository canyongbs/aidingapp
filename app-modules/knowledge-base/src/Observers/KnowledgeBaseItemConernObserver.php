<?php

namespace AidingApp\KnowledgeBase\Observers;

use AidingApp\KnowledgeBase\Models\KnowledgeBaseItemConcern;
use AidingApp\KnowledgeBase\Notifications\KnowledgeBaseItemConcernCreated;
use AidingApp\KnowledgeBase\Notifications\KnowledgeBaseItemConcernStatusChanged;

class KnowledgeBaseItemConernObserver
{
    public function created(KnowledgeBaseItemConcern $knowledgeBaseItemConcern): void
    {
        $knowledgeBaseItemConcern->createdBy->notifyNow(new KnowledgeBaseItemConcernCreated($knowledgeBaseItemConcern));
    }
    
    public function updated(KnowledgeBaseItemConcern $knowledgeBaseItemConcern): void
    {
        if($knowledgeBaseItemConcern->wasChanged('status')) {
            $knowledgeBaseItemConcern->createdBy->notifyNow(new KnowledgeBaseItemConcernStatusChanged($knowledgeBaseItemConcern));
        }
    }
}