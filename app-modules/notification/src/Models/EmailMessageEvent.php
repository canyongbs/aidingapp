<?php

namespace AidingApp\Notification\Models;

use AidingApp\Notification\Enums\EmailMessageEventType;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperEmailMessageEvent
 */
class EmailMessageEvent extends BaseModel
{
    // TODO: Create Factory

    protected $fillable = [
        'type',
        'payload',
        'occurred_at',
    ];

    protected $casts = [
        'type' => EmailMessageEventType::class,
        'payload' => 'array',
        'occurred_at' => 'datetime',
    ];

    public function message(): BelongsTo
    {
        return $this->belongsTo(EmailMessage::class);
    }
}
