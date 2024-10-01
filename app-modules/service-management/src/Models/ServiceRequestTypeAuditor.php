<?php

namespace AidingApp\ServiceManagement\Models;

use AidingApp\Team\Models\Team;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceRequestTypeAuditor extends Pivot
{
    use HasFactory;
    use HasUuids;

    protected $table = 'service_request_type_auditors';

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function serviceRequestType(): BelongsTo
    {
        return $this->belongsTo(ServiceRequestType::class);
    }
}
