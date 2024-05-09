<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphTo;

class Tag extends BaseModel
{
    protected $fillable = [
        'name',
        'type',
    ];
}
