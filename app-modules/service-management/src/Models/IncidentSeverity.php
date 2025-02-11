<?php

namespace AidingApp\ServiceManagement\Models;

use AidingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use AidingApp\ServiceManagement\Enums\IncidentSeverityColorOptions;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @mixin IdeHelperIncidentSeverity
 */
class IncidentSeverity extends BaseModel implements Auditable
{
    use AuditableTrait;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'color',
    ];

    protected $casts = [
        'color' => IncidentSeverityColorOptions::class,
    ];

    public function incidents(): HasMany
    {
        return $this->hasMany(Incident::class, 'severity_id');
    }
}
