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

namespace AidingApp\ServiceManagement\Jobs;

use AidingApp\ServiceManagement\Actions\EndServiceRequestConversation;
use AidingApp\ServiceManagement\Enums\ServiceRequestConversationFinishedReason;
use AidingApp\ServiceManagement\Events\ServiceRequestConversationExpired;
use AidingApp\ServiceManagement\Models\ServiceRequestConversation;
use App\Models\User;
use App\Settings\PresenceSettings;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class EndServiceRequestConversations implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $uniqueFor = 900;

    public function handle(): void
    {
        $this->expireQueuedConversations();
        $this->endInactiveConversations();
    }

    protected function expireQueuedConversations(): void
    {
        ServiceRequestConversation::query()
            ->whereNotNull('queued_at')
            ->whereNull('accepted_at')
            ->whereNull('finished_at')
            ->where('queued_at', '<', now()->subMinutes(5))
            ->eachById(function (ServiceRequestConversation $record) {
                $record->update([
                    'finished_at' => now(),
                    'finished_reason' => ServiceRequestConversationFinishedReason::QueueExpired,
                ]);

                broadcast(new ServiceRequestConversationExpired($record));
            });

        ServiceRequestConversation::query()
            ->whereNotNull('queued_at')
            ->whereNull('accepted_at')
            ->whereNull('finished_at')
            ->whereHas('user', function (Builder $query) {
                $threshold = app(PresenceSettings::class)->active_threshold;

                $query->where(function (Builder $query) use ($threshold) {
                    $query->whereNull('last_activity_at')
                        ->orWhere('last_activity_at', '<', now()->subMinutes($threshold));
                });
            })
            ->eachById(function (ServiceRequestConversation $record) {
                $record->update([
                    'finished_at' => now(),
                    'finished_reason' => ServiceRequestConversationFinishedReason::AgentInactive,
                ]);

                broadcast(new ServiceRequestConversationExpired($record));
            });
    }

    protected function endInactiveConversations(): void
    {
        ServiceRequestConversation::query()
            ->whereNotNull('accepted_at')
            ->whereNull('finished_at')
            ->whereNotNull('conversation_id')
            ->eachById(function (ServiceRequestConversation $record) {
                $lastMessage = $record->conversation?->messages()->latest()->first();

                $lastActivity = $lastMessage->created_at ?? $record->accepted_at;

                if ($lastActivity >= now()->subMinutes(20)) {
                    return;
                }

                $reason = ServiceRequestConversationFinishedReason::AgentInactive;

                if ($lastMessage) {
                    $isAgentLastAuthor = $lastMessage->author_type === (new User())->getMorphClass();
                    $reason = $isAgentLastAuthor
                        ? ServiceRequestConversationFinishedReason::ContactInactive
                        : ServiceRequestConversationFinishedReason::AgentInactive;
                }

                app(EndServiceRequestConversation::class)->execute($record, $reason);
            });
    }
}
