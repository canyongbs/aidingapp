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

namespace AidingApp\InAppCommunication\Models;

use AidingApp\InAppCommunication\Database\Factories\MessageFactory;
use App\Models\BaseModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @mixin IdeHelperMessage
 */
class Message extends BaseModel
{
    /** @use HasFactory<MessageFactory> */
    use HasFactory;

    use HasUuids;

    protected $fillable = [
        'conversation_id',
        'author_type',
        'author_id',
        'content',
    ];

    protected $casts = [
        'content' => 'array',
    ];

    /**
     * @return BelongsTo<Conversation, $this>
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function author(): MorphTo
    {
        return $this->morphTo();
    }

    public function mentionsUser(User $user): bool
    {
        return in_array($user->getKey(), $this->getMentionedUserIds(), true);
    }

    /**
     * @return array<int, string>
     */
    public function getMentionedUserIds(): array
    {
        $userIds = [];
        $this->extractMentionIds($this->content, $userIds);

        return array_unique($userIds);
    }

    /**
     * @param  array<string, mixed>  $content
     * @param  array<int, string>  $userIds
     */
    protected function extractMentionIds(array $content, array &$userIds): void
    {
        if (isset($content['type']) && $content['type'] === 'mention') {
            if (isset($content['attrs']['id']) && is_string($content['attrs']['id'])) {
                $userIds[] = $content['attrs']['id'];
            }
        }

        if (isset($content['content']) && is_array($content['content'])) {
            foreach ($content['content'] as $child) {
                if (is_array($child)) {
                    $this->extractMentionIds($child, $userIds);
                }
            }
        }
    }
}
