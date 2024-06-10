<?php

namespace AidingApp\Contact\Models;

use App\Models\User;
use DateTimeInterface;
use App\Models\BaseModel;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Organization extends BaseModel implements HasMedia
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;
    use InteractsWithMedia;

    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'website',
        'industry_id',
        'type_id',
        'description',
        'number_of_employees',
        'address',
        'city',
        'state',
        'postalcode',
        'country',
        'linkedin_url',
        'facebook_url',
        'twitter_url',
        'created_by_id',
    ];

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('organization_logo')
            ->useDisk('s3')
            ->singleFile()
            ->acceptsMimeTypes([
                'image/png',
                'image/jpeg',
                'image/gif',
                'image/webp',
                'image/jpg',
            ]);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function industry(): BelongsTo
    {
        return $this->belongsTo(OrganizationIndustry::class);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(OrganizationType::class);
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format(config('project.datetime_format') ?? 'Y-m-d H:i:s');
    }
}
