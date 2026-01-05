<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

namespace AidingApp\Ai\Models;

use AidingApp\Ai\Database\Factories\AiAssistantFactory;
use AidingApp\Ai\Enums\AiAssistantApplication;
use AidingApp\Ai\Enums\AiModel;
use Illuminate\Database\Eloquent\Concerns\HasVersion4Uuids as HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @mixin IdeHelperAiAssistant
 */
class AiAssistant extends Model implements HasMedia
{
    /** @use HasFactory<AiAssistantFactory> */
    use HasFactory;

    use HasUuids;
    use InteractsWithMedia;
    use SoftDeletes;

    protected $fillable = [
        'archived_at',
        'assistant_id',
        'name',
        'application',
        'model',
        'is_default',
        'description',
        'instructions',
        'knowledge',
        'is_confidential',
        'created_by_id',
    ];

    protected $casts = [
        'application' => AiAssistantApplication::class,
        'archived_at' => 'datetime',
        'is_default' => 'bool',
        'model' => AiModel::class,
    ];

    /**
     * @return HasMany<AiAssistantFile, $this>
     */
    public function files(): HasMany
    {
        return $this->hasMany(AiAssistantFile::class, 'assistant_id');
    }

    public function isDefault(): bool
    {
        return $this->is_default ?? false;
    }
}
