<?php

namespace AidingApp\Project\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\MorphPivot;

class ProjectGuest extends MorphPivot
{
    use HasUuids;

    protected $fillable = [
        'project_id',
        'guest_id',
        'guest_type',
    ];
}
