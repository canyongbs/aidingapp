<?php

namespace AidingApp\ServiceManagement\Models;

use App\Models\BaseModel;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use AidingApp\ServiceManagement\Observers\ProductObserver;
use AidingApp\Audit\Models\Concerns\Auditable as AuditableTrait;

#[ObservedBy(ProductObserver::class)]

class Product extends BaseModel implements Auditable
{
    use AuditableTrait;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'url',
        'description',
        'version',
        'additional_notes',
    ];

    public function product_licenses(): HasMany
    {
        return $this->hasMany(ProductLicense::class, 'product_id');
    }
}
