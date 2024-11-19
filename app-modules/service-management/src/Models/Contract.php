<?php

namespace AidingApp\ServiceManagement\Models;

use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use AidingApp\ServiceManagement\Enums\ContractStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use AidingApp\ServiceManagement\Casts\ContractCurrencyValue;
use AidingApp\Audit\Models\Concerns\Auditable as AuditableTrait;

/**
 * @mixin IdeHelperContract
 */
class Contract extends Model implements HasMedia, Auditable
{
    use HasFactory;
    use HasUuids;
    use InteractsWithMedia;
    use AuditableTrait;

    protected $append = ['status'];

    protected $fillable = [
        'name',
        'description',
        'contract_type',
        'vendor_name',
        'start_date',
        'end_date',
        'contract_value',
    ];

    protected $casts = [
        'contract_value' => ContractCurrencyValue::class,
    ];

    public function contractType(): BelongsTo
    {
        return $this->belongsTo(ContractType::class, 'contract_type', 'id');
    }

    public function getStatusAttribute(): ContractStatus
    {
        return ContractStatus::getStatus($this->start_date, $this->end_date);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('contract_files')
            ->acceptsMimeTypes([
                'application/pdf' => ['pdf'],
                'application/vnd.ms-word' => ['doc'],
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => ['docx'],
                'image/pdf' => ['pdf'],
            ]);
    }
}
