<?php

namespace AidingApp\ServiceManagement\Models;

use AidingApp\ServiceManagement\Database\Factories\TenantServiceRequestTypeDomainFactory;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Multitenancy\Models\Concerns\UsesLandlordConnection;

/**
 * @mixin IdeHelperTenantServiceRequestTypeDomain
 */
class TenantServiceRequestTypeDomain extends Model
{
    /** @use HasFactory<TenantServiceRequestTypeDomainFactory> */
    use HasFactory;

    use HasUuids;
    use UsesLandlordConnection;
    // TODO: Add Auditing

    protected $fillable = [
        'tenant_id',
        'service_request_type_id',
        'domain',
    ];

    /**
     * @return BelongsTo<ServiceRequestType, $this>
     */
    public function serviceRequestType(): BelongsTo
    {
        return $this->belongsTo(ServiceRequestType::class, 'service_request_type_id');
    }

    /**
     * @return BelongsTo<Tenant, $this>
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }
}
