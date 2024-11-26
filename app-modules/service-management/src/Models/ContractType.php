<?php

namespace AidingApp\ServiceManagement\Models;

use App\Models\BaseModel;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use AidingApp\Audit\Models\Concerns\Auditable as AuditableTrait;

/**
 * @mixin IdeHelperContractType
 */
class ContractType extends BaseModel implements Auditable
{
    use AuditableTrait;

    protected $fillable = ['name', 'is_default', 'order'];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class, 'contract_type');
    }
}
