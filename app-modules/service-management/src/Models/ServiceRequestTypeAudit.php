<?php

namespace AidingApp\ServiceManagement\Models;

use AidingApp\Team\Models\Team;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceRequestTypeAudit extends Pivot
{
    use HasFactory;
    use HasUuids;

    public function audit(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function serviceRequestType(): BelongsTo
    {
        return $this->belongsTo(ServiceRequestType::class);
    }
}
