<?php

namespace AidingApp\Notification\Models;

use AidingApp\Notification\Enums\SmsMessageEventType;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperSmsMessageEvent
 */
class SmsMessageEvent extends BaseModel
{
    // TODO: Create Factory

    protected $fillable = [
        'type',
        'payload',
        'occurred_at',
    ];

    protected $casts = [
        'type' => SmsMessageEventType::class,
        'payload' => 'array',
        'occurred_at' => 'datetime',
    ];

    public function message(): BelongsTo
    {
        return $this->belongsTo(SmsMessage::class);
    }
}
