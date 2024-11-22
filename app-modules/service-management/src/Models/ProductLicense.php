<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

    Aiding App™ is licensed under the Elastic License 2.0. For more details,
    see <https://github.com/canyongbs/aidingapp/blob/main/LICENSE.>

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Aiding App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

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