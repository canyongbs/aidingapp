<?php

namespace AidingApp\ServiceManagement\Models;

use AidingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use AidingApp\ServiceManagement\Enums\SystemIncidentStatusClassification;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @mixin IdeHelperIncidentStatus
 */
class IncidentStatus extends BaseModel implements Auditable
{
    use AuditableTrait;
    use SoftDeletes;

    protected $fillable = [
        'classification',
        'name',
    ];

    protected $casts = [
        'classification' => SystemIncidentStatusClassification::class,
    ];

    public function incidents(): HasMany
    {
        return $this->hasMany(Incident::class, 'status_id');
    }
}
