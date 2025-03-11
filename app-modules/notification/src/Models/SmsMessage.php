<?php

namespace AidingApp\Notification\Models;

use AidingApp\Notification\Models\Contracts\Message;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @mixin IdeHelperSmsMessage
 */
class SmsMessage extends BaseModel implements Message
{
    protected $fillable = [
        'notification_class',
        'external_reference_id',
        'content',
        'quota_usage',
        'recipient_id',
        'recipient_type',
    ];

    protected $casts = [
        'content' => 'array',
    ];

    public function related(): MorphTo
    {
        return $this->morphTo(
            name: 'related',
            type: 'related_type',
            id: 'related_id',
        );
    }

    public function recipient(): MorphTo
    {
        return $this->morphTo(
            name: 'recipient',
            type: 'recipient_type',
            id: 'recipient_id',
        );
    }

    public function events(): HasMany
    {
        return $this->hasMany(SmsMessageEvent::class);
    }
}
