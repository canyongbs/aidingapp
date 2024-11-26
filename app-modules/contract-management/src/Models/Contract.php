<?php

namespace AidingApp\ContractManagement\Models;

use App\Casts\CurrencyCast;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use AidingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use AidingApp\ContractManagement\Enums\ContractStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;

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
