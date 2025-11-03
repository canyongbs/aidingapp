<?php

namespace AidingApp\Ai\Models;

use AidingApp\Ai\Database\Factories\AiMessageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\Concerns\AsPivot;
use Illuminate\Database\Eloquent\SoftDeletes;

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
}
