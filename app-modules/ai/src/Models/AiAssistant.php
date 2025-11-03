<?php

namespace AidingApp\Ai\Models;

use AidingApp\Ai\Database\Factories\AiAssistantFactory;
use AidingApp\Ai\Enums\AiAssistantApplication;
use AidingApp\Ai\Enums\AiModel;
use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class AiAssistant extends Model implements HasMedia
{
    /** @use HasFactory<AiAssistantFactory> */
    use HasFactory;

    use HasUuids;
    use InteractsWithMedia;
    use SoftDeletes;

    protected $fillable = [
        'archived_at',
        'assistant_id',
        'name',
        'application',
        'model',
        'description',
        'instructions',
        'knowledge',
        'is_confidential',
        'created_by_id',
    ];

    protected $casts = [
        'application' => AiAssistantApplication::class,
        'archived_at' => 'datetime',
        'model' => AiModel::class,
    ];
}
