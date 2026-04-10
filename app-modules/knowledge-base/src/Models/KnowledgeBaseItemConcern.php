<?php

namespace AidingApp\KnowledgeBase\Models;

use AidingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use AidingApp\KnowledgeBase\Database\Factories\KnowledgeBaseItemConcernFactory;
use AidingApp\KnowledgeBase\Enums\ConcernStatus;
use AidingApp\KnowledgeBase\Observers\KnowledgeBaseItemConernObserver;
use App\Models\BaseModel;
use CanyonGBS\Common\Models\Concerns\HasUserSaveTracking;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

#[ObservedBy(KnowledgeBaseItemConernObserver::class)]
class KnowledgeBaseItemConcern extends BaseModel implements Auditable
{
    /** @use HasFactory<KnowledgeBaseItemConcernFactory> */
    use HasFactory;
    use SoftDeletes;
    use AuditableTrait;
    use HasUuids;
    use HasUserSaveTracking;

    protected $fillable = [
        'description',
        'status',
    ];

    protected $casts = [
        'status' => ConcernStatus::class,
    ];

    /**
     * @return BelongsTo<KnowledgeBaseItem, $this>
     */
    public function knowledgeBaseItem(): BelongsTo
    {
        return $this->belongsTo(KnowledgeBaseItem::class);
    }
}
