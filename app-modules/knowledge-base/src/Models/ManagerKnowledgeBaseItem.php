<?php

namespace AidingApp\KnowledgeBase\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @mixin IdeHelperManagerKnowledgeBaseItem
 */
class ManagerKnowledgeBaseItem extends Pivot
{
    use HasUuids;

    /**
     * @return BelongsTo<User, $this>
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * @return BelongsTo<KnowledgeBaseItem, $this>
     */
    public function knowledgeBaseItem(): BelongsTo
    {
        return $this->belongsTo(KnowledgeBaseItem::class);
    }
}
