<?php

namespace AidingApp\ServiceManagement\Models;

use AidingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class HistoricalServiceMonitoring extends BaseModel implements Auditable
{
    use AuditableTrait;
    use SoftDeletes;

    protected $fillable = [
        'response',
        'service_monitoring_target_id',
    ];

    public function serviceMonitoringTarget(): HasOne
    {
        return $this->hasOne(ServiceMonitoringTarget::class);
    }
}
