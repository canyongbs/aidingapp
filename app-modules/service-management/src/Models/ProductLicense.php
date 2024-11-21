<?php

namespace AidingApp\ServiceManagement\Models;

use Carbon\Carbon;
use App\Models\BaseModel;
use AidingApp\Contact\Models\Contact;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use AidingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use AidingApp\ServiceManagement\Observers\ProductLicenseObserver;

#[ObservedBy(ProductLicenseObserver::class)]

class ProductLicense extends BaseModel implements Auditable
{
    use AuditableTrait;
    use SoftDeletes;

    protected $fillable = [
        'license',
        'description',
        'start_date',
        'expiration_date',
        'additional_notes',
    ];

    protected $casts = [
        'license' => 'encrypted',
        'start_date' => 'date',
        'expiration_date' => 'date',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['status'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'assigned_to', 'id');
    }

    protected function Status(): Attribute
    {
        return new Attribute(
            get: function () {
                $today = Carbon::today();

                $startDate = $this->getAttribute('start_date');
                $endDate = $this->getAttribute('end_date');

                if ($today->lt($startDate)) {
                    return 'Pending';
                } elseif ($today->between($startDate, $endDate)) {
                    return 'Active';
                } elseif ($today->gt($endDate)) {
                    return 'Expired';
                }

                return null;
            },
        );
    }
}
