<?php

namespace AidingApp\Contact\Models;

use App\Models\User;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrganizationType extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;
    

    protected $fillable = [
        'name',
        'is_default',
        'created_by_id'
    ];

    public function organisations(): HasMany
    {
        return $this->hasMany(Organization::class, 'type_id');
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(config('project.datetime_format') ?? 'Y-m-d H:i:s');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
