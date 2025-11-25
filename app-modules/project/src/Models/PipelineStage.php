<?php

namespace AidingApp\Project\Models;

use AidingApp\Project\Database\Factories\PipelineStageFactory;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperPipelineStage
 */
class PipelineStage extends BaseModel
{
    /** @use HasFactory<PipelineStageFactory> */
    use HasFactory;

    use HasUuids;

    protected $fillable = [
        'name',
        'pipeline_id',
        'order',
    ];

    /**
     * @return BelongsTo<Pipeline, $this>
     */
    public function pipeline(): BelongsTo
    {
        return $this->belongsTo(Pipeline::class);
    }
}
