<?php

namespace AidingApp\Ai\Models;

use AidingApp\Ai\Enums\AiMessageLogFeature;
use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegacyAiMessageLog extends BaseModel
{
    protected $table = 'assistant_chat_message_logs';

    protected $fillable = [
        'message',
        'metadata',
        'request',
        'sent_at',
        'user_id',
        'ai_assistant_name',
        'feature',
    ];

    protected $casts = [
        'metadata' => 'encrypted:array',
        'request' => 'encrypted:array',
        'sent_at' => 'datetime',
        'feature' => AiMessageLogFeature::class,
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}