<?php

namespace AidingApp\Portal\Http\Routing;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseCategory;

class CategoryShowMissingHandler
{
    public function __invoke(Request $request)
    {
        throw_if(! str()->isUuid($request->category), ModelNotFoundException::class);

        $category = KnowledgeBaseCategory::findOrFail($request->category);

        return redirect()->route('api.portal.category.show', $category->slug);
    }
}
