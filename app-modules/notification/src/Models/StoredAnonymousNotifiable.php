<?php

namespace AidingApp\Notification\Models;

use AidingApp\Notification\Enums\NotificationChannel;
use App\Models\BaseModel;

class StoredAnonymousNotifiable extends BaseModel
{
    protected $fillable = [
        'type',
        'route',
    ];

    protected $casts = [
        'type' => NotificationChannel::class,
    ];
}
