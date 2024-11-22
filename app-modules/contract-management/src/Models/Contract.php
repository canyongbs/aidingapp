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

namespace AidingApp\ContractManagement\Models;

use App\Models\BaseModel;
use App\Casts\CurrencyCast;
use Spatie\MediaLibrary\HasMedia;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use AidingApp\ContractManagement\Enums\ContractStatus;
use AidingApp\Audit\Models\Concerns\Auditable as AuditableTrait;

/**
 * @mixin IdeHelperContract
 */
class Contract extends BaseModel implements HasMedia, Auditable
{
    use InteractsWithMedia;
    use AuditableTrait;
    use SoftDeletes;

    protected $append = ['status'];

    protected $fillable = [
        'name',
        'description',
        'vendor_name',
        'start_date',
        'end_date',
        'contract_value',
        'contract_type_id',
    ];

    protected $casts = [
        'contract_value' => CurrencyCast::class,
    ];

    public function contractType(): BelongsTo
    {
        return $this->belongsTo(ContractType::class, 'contract_type_id', 'id');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('contract_files')
            ->onlyKeepLatest(5)
            ->acceptsMimeTypes([
                'application/pdf' => ['pdf'],
                'application/vnd.ms-word' => ['doc'],
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => ['docx'],
                'image/pdf' => ['pdf'],
            ]);
    }

    protected function status(): Attribute
    {
        return Attribute::make(
            get: fn () => ContractStatus::getStatus($this->start_date, $this->end_date),
        );
    }
}