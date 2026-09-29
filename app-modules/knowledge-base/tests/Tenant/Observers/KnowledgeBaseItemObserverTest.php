<?php

use AidingApp\KnowledgeBase\Models\KnowledgeBaseItem;

it('assigns a public ID when creating an article', function () {
    $article = KnowledgeBaseItem::factory()->create();

    expect($article->public_id)
        ->toHaveLength(8)
        ->toMatch('/^[0-9A-Za-z]{8}$/');
});
