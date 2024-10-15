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

namespace AidingApp\KnowledgeBase\Models;

use DateTimeInterface;
use App\Models\BaseModel;
use App\Models\Contracts\HasTags;
use Spatie\MediaLibrary\HasMedia;
use AidingApp\Division\Models\Division;
use OwenIt\Auditing\Contracts\Auditable;
use App\Models\Concerns\InteractsWithTags;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use AidingApp\Audit\Models\Concerns\Auditable as AuditableTrait;

/**
 * @mixin IdeHelperKnowledgeBaseItem
 */
class KnowledgeBaseItem extends BaseModel implements Auditable, HasMedia, HasTags
{
  use AuditableTrait;
  use HasUuids;
  use InteractsWithMedia;
  use SoftDeletes;
  use InteractsWithTags;

  protected $table = 'knowledge_base_articles';

  protected $casts = [
    'featured' => 'boolean',
    'public' => 'boolean',
    'article_details' => 'array',
  ];

  protected $fillable = [
    'article_details',
    'category_id',
    'notes',
    'public',
    'quality_id',
    'status_id',
    'title',
    'featured'
  ];

  public function quality(): BelongsTo
  {
    return $this->belongsTo(KnowledgeBaseQuality::class);
  }

  public function status(): BelongsTo
  {
    return $this->belongsTo(KnowledgeBaseStatus::class);
  }

  public function category(): BelongsTo
  {
    return $this->belongsTo(KnowledgeBaseCategory::class);
  }

  public function division(): BelongsToMany
  {
    return $this->belongsToMany(Division::class);
  }

  public function registerMediaCollections(): void
  {
    $this->addMediaCollection('article_details');
  }

  public function scopePublic($query)
  {
    return $query->where('public', true);
  }

  protected function serializeDate(DateTimeInterface $date): string
  {
    return $date->format(config('project.datetime_format') ?? 'Y-m-d H:i:s');
  }
}
