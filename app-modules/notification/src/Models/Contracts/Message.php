<?php

namespace AidingApp\Notification\Models\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphTo;

interface Message
{
    public function related(): MorphTo;

    public function recipient(): MorphTo;
}
