<?php

namespace AidingApp\Portal\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PortalGuest extends BaseModel
{
    use HasUuids;
    use SoftDeletes;

    public function user()
    {
        return $this->morphOne(KnowledgeBaseArticleVote::class, 'morphable');
    }
}
