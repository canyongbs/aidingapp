<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Aiding App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Aiding App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AidingApp\InAppCommunication\Jobs;

use AidingApp\InAppCommunication\Enums\ConversationEphemeralPeriod;
use AidingApp\InAppCommunication\Events\MessagesPruned;
use AidingApp\InAppCommunication\Events\UnreadCountUpdated;
use AidingApp\InAppCommunication\Models\Conversation;
use AidingApp\InAppCommunication\Models\ConversationParticipant;
use AidingApp\InAppCommunication\Models\Message;
use App\Features\ConfidentialChannelsFeature;
use Carbon\CarbonInterface;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class PruneEphemeralMessages implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $uniqueFor = 300;

    public function handle(): void
    {
        if (! ConfidentialChannelsFeature::active()) {
            return;
        }

        foreach (ConversationEphemeralPeriod::cases() as $period) {
            $cutoff = $period->subtractFrom(now());

            Conversation::query()
                ->where('is_confidential', true)
                ->where('ephemeral_period', $period)
                ->whereHas('messages', fn (Builder $query) => $query->where('created_at', '<', $cutoff))
                ->eachById(fn (Conversation $conversation) => $this->pruneConversation($conversation, $cutoff));
        }
    }

    public function failed(?Throwable $exception): void
    {
        if ($exception) {
            report($exception);
        }
    }

    protected function pruneConversation(Conversation $conversation, CarbonInterface $cutoff): void
    {
        broadcast(new MessagesPruned($conversation, $cutoff));

        $conversation->messages()
            ->where('created_at', '<', $cutoff)
            ->chunkById(500, fn (Collection $messages) => $messages->each->delete());

        $this->clampUnreadCounts($conversation);
    }

    protected function clampUnreadCounts(Conversation $conversation): void
    {
        $remainingForParticipant = Message::query()
            ->selectRaw('count(*)')
            ->whereColumn('messages.conversation_id', 'conversation_participants.conversation_id')
            ->where(fn (Builder $query) => $query
                ->whereNull('conversation_participants.last_read_at')
                ->orWhereColumn('messages.created_at', '>', 'conversation_participants.last_read_at'));

        $conversation->conversationParticipants()
            ->where('unread_count', '>', 0)
            ->select('conversation_participants.*')
            ->selectSub($remainingForParticipant, 'remaining_count')
            ->lazyById()
            ->each(function (ConversationParticipant $participant) use ($conversation) {
                $remaining = (int) $participant->getAttribute('remaining_count');

                if ($participant->unread_count <= $remaining) {
                    return;
                }

                $participant->unread_count = $remaining;
                $participant->save();

                broadcast(new UnreadCountUpdated(
                    userId: $participant->participant_id,
                    conversationId: $conversation->getKey(),
                    unreadCount: $remaining,
                ));
            });
    }
}
