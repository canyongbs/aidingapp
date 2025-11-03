<?php

namespace AidingApp\IntegrationOpenAi\Models;

use AidingApp\IntegrationOpenAi\Database\Factories\OpenAiVectorStoreFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class OpenAiVectorStore extends Model
{
    /** @use HasFactory<OpenAiVectorStoreFactory> */
    use HasFactory;

    use HasUuids;
    use SoftDeletes;

    public $fillable = [
        'deployment_hash',
        'ready_until',
        'vector_store_id',
        'vector_store_file_id',
    ];

    protected $casts = [
        'ready_until' => 'immutable_datetime',
    ];

    /**
     * @return MorphTo<Model, $this>
     */
    public function file(): MorphTo
    {
        return $this->morphTo('file');
    }
}
