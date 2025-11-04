<?php

namespace AidingApp\Ai\Models;

use AidingApp\Ai\Database\Factories\AiThreadFactory;
use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @mixin IdeHelperAiThread
 */
class AiThread extends Model implements HasMedia
{
    /** @use HasFactory<AiThreadFactory> */
    use HasFactory;

    use HasUuids;
    use SoftDeletes;
    use Prunable;
    use InteractsWithMedia;

    protected $fillable = [
        'thread_id',
        'name',
        'assistant_id',
        'folder_id',
        'user_id',
        'locked_at',
        'locked_reason',
    ];

    protected $casts = [
        'locked_at' => 'datetime',
        'saved_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<AiAssistant, $this>
     */
    public function assistant(): BelongsTo
    {
        return $this->belongsTo(AiAssistant::class, 'assistant_id');
    }

    /**
     * @return HasMany<AiMessage, $this>
     */
    public function messages(): HasMany
    {
        return $this->hasMany(AiMessage::class, 'thread_id');
    }

    /**
     * @return BelongsToMany<User, $this>
     */
    public function users(): BelongsToMany
    {
        /** @phpstan-ignore argument.templateType (We are using some Laravel magic here to use Tenant as a Pivot without actually being a pivot) */
        return $this->belongsToMany(
            User::class,
            table: 'ai_messages',
            foreignPivotKey: 'thread_id',
        )->using(AiMessage::class); // @phpstan-ignore argument.type (Same as above)
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
