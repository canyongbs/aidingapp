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

namespace AidingApp\InAppCommunication\Models\Scopes;

use AidingApp\InAppCommunication\Models\Conversation;
use AidingApp\InAppCommunication\Models\ConversationParticipant;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class WithCurrentParticipant
{
    public function __construct(
        protected User $user,
    ) {}

    /**
     * @param Builder<Conversation> $query
     */
    public function __invoke(Builder $query): void
    {
        $userType = $this->user->getMorphClass();
        $userId = $this->user->getKey();

        $subQuery = ConversationParticipant::query()
            ->whereColumn('conversation_id', 'conversations.id')
            ->where('participant_type', $userType)
            ->where('participant_id', $userId)
            ->limit(1);

        $query->addSelect([
            'current_participant_is_pinned' => (clone $subQuery)->select('is_pinned'),
            'current_participant_notification_preference' => (clone $subQuery)->select('notification_preference'),
            'current_participant_last_read_at' => (clone $subQuery)->select('last_read_at'),
        ]);
    }
}
