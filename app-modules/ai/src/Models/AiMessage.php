<?php

namespace AidingApp\Ai\Models;

use AidingApp\Ai\Database\Factories\AiMessageFileFactory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Concerns\AsPivot;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperAiMessage
 */
class AiMessage extends Model
{
    /** @use HasFactory<AiMessageFileFactory> */
    use HasFactory;

    use AsPivot;
    use SoftDeletes;

    protected $fillable = [
        'message_id',
        'content',
        'context',
        'request',
        'thread_id',
        'user_id',
        'prompt_id',
    ];

    protected $casts = [
        'request' => 'encrypted:array',
    ];

    protected $table = 'ai_messages';

    /**
     * @return BelongsTo<AiThread, $this>
     */
    public function thread(): BelongsTo
    {
        return $this->belongsTo(AiThread::class, 'thread_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Prompt, $this>
     */
    public function prompt(): BelongsTo
    {
        return $this->belongsTo(Prompt::class)->withTrashed();
    }

    /**
     * @return HasMany<AiMessageFile, $this>
     */
    public function files(): HasMany
    {
        return $this->hasMany(AiMessageFile::class, 'message_id');
    }
}
