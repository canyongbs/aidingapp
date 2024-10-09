<?php

namespace AidingApp\Portal\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseItem;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KnowledgeBaseArticleVote extends BaseModel
{
    use HasUuids;

    protected $casts = [
        'is_helpful' => 'boolean',
    ];

    protected $fillable = [
        'is_helpful',
        'user_id',
        'user_type',
        'article_id',
    ];

    public function morphable()
    {
        return $this->morphTo();
    }

    public function knowledgeBaseArticle(): BelongsTo
    {
        return $this->belongsTo(KnowledgeBaseItem::class);
    }
}
