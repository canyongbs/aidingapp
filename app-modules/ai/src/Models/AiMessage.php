<?php

namespace AidingApp\Ai\Models;

use AidingApp\Ai\Database\Factories\AiMessageFactory;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Concerns\AsPivot;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperAiMessage
 */
class AiMessage extends Model
{
    /** @use HasFactory<AiMessageFactory> */
    use HasFactory;

    use HasUuids;
    use AsPivot;
    use Prunable;
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

    public function prunable(): Builder
    {
        return static::query()
            ->whereNotNull('deleted_at')
            ->where('deleted_at', '<=', now()->subDays(7))
            ->whereDoesntHave('files', fn (Builder $query) => $query->withTrashed());
    }
}
