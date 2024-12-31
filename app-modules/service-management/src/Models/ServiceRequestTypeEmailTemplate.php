<?php

namespace AidingApp\ServiceManagement\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceRequestTypeEmailTemplate extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'service_request_type_id',
        'type',
        'subject',
        'body',
    ];

    protected $casts = [
        'subject' => 'array',
        'body' => 'array',
    ];
}
