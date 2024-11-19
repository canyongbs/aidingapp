<?php

namespace AidingApp\ServiceManagement\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use AidingApp\Audit\Models\Concerns\Auditable as AuditableTrait;

/**
 * @mixin IdeHelperContractType
 */
class ContractType extends Model implements Auditable
{
    use HasFactory;
    use HasUuids;
    use AuditableTrait;

    protected $fillable = ['name', 'is_default', 'order'];

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class, 'contract_type');
    }
}
