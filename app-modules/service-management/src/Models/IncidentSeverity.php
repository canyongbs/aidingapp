<?php

namespace AidingApp\ServiceManagement\Models;

use AidingApp\Audit\Models\Concerns\Auditable as AuditableTrait;
use App\Models\BaseModel;
use Filament\Support\Colors\Color;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @mixin IdeHelperIncidentSeverity
 */
class IncidentSeverity extends BaseModel implements Auditable
{
    use AuditableTrait;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'color',
    ];

    protected $appends = [
        'rgb_color',
    ];

    public function incidents(): HasMany
    {
        return $this->hasMany(Incident::class, 'severity_id');
    }

    protected function rgbColor(): Attribute
    {
        return new Attribute(
            get: fn () => 'rgb(' . Color::all()[$this->color][600] . ')',
        );
    }
}
