<?php

namespace AidingApp\KnowledgeBase\Models;

use AidingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use AidingApp\KnowledgeBase\Enums\SystemIncidentStatusClassification;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @mixin IdeHelperIncidentStatus
 */
class IncidentStatus extends BaseModel implements Auditable
{
    use AuditableTrait;
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    protected $fillable = [
        'classification',
        'name',
        // 'color',
    ];

    protected $casts = [
        'classification' => SystemIncidentStatusClassification::class,
    ];
}
