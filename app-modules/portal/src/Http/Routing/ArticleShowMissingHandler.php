<?php

namespace AidingApp\Portal\Http\Routing;

use Illuminate\Http\Request;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseItem;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseCategory;

class ArticleShowMissingHandler
{
    public function __invoke(Request $request)
    {
        throw_if(
            ! $request->category instanceof KnowledgeBaseCategory
            && ! str()->isUuid($request->category),
            ModelNotFoundException::class
        );

        $category = $request->category instanceof KnowledgeBaseCategory ? $request->category : KnowledgeBaseCategory::findOrFail($request->category);

        throw_if(
            ! $request->article instanceof KnowledgeBaseItem
            && ! str()->isUuid($request->article),
            ModelNotFoundException::class
        );

        $article = $request->article instanceof KnowledgeBaseItem ? $request->article : KnowledgeBaseItem::findOrFail($request->article);

        return redirect()->route('api.portal.article.show', [
            'category' => $category,
            'article' => $article,
        ]);
    }
}
