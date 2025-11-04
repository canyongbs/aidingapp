<?php

namespace AidingApp\Ai\Models;

use App\Models\BaseModel;
use Database\Factories\PortalAssistantMessageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PortalAssistantMessage extends BaseModel
{
    /** @use HasFactory<PortalAssistantMessageFactory> */
    use HasFactory;

    public $fillable = [
        'message_id',
        'content',
        'context',
        'request',
        'next_request_options',
        'thread_id',
        'author_type',
        'author_id',
        'is_advisor',
    ];

    protected $casts = [
        'next_request_options' => 'array',
        'request' => 'encrypted:array',
        'is_advisor' => 'boolean',
    ];

    /**
     * @return BelongsTo<PortalAssistantThread, $this>
     */
    public function thread(): BelongsTo
    {
        return $this->belongsTo(PortalAssistantThread::class, 'thread_id');
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function author(): MorphTo
    {
        return $this->morphTo('author');
    }
}
