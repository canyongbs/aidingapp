<?php

namespace AidingApp\Ai\Models;

use AidingApp\Ai\Database\Factories\PromptTypeFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class PromptType extends Model
{
    /** @use HasFactory<PromptTypeFactory> */
    use HasFactory;

    use HasUuids;
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
    ];
}
