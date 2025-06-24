<?php

namespace AidingApp\Engagement\Models;

use AidingApp\Engagement\Database\Factories\UnmatchedInboundCommunicationFactory;
use AidingApp\Engagement\Enums\EngagementResponseType;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @mixin IdeHelperUnmatchedInboundCommunication
 */
class UnmatchedInboundCommunication extends BaseModel
{
    /** @use HasFactory<UnmatchedInboundCommunicationFactory> */
    use HasFactory;

    protected $fillable = [
        'sender',
        'occurred_at',
        'subject',
        'type',
        'body',
    ];

    protected $casts = [
        'occurred_at' => 'datetime',
        'type' => EngagementResponseType::class,
    ];
}
