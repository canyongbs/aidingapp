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

namespace AidingApp\InAppCommunication\Services;

use Ably\AblyRest;
use Ably\Exceptions\AblyException;

class ConversationPresence
{
    protected ?AblyRest $ably = null;

    /**
     * @return array<int, string>
     */
    public function getPresentUserIds(string $conversationId): array
    {
        $ably = $this->getAbly();

        if (! $ably) {
            return [];
        }

        try {
            // Ably's Pusher adapter uses colon prefix for presence channels
            $channel = $ably->channel("presence:conversation.{$conversationId}");
            $presenceMessages = $channel->presence->get();

            $userIds = [];

            foreach ($presenceMessages->items as $member) {
                $data = $member->data;

                if (is_object($data) && isset($data->id)) {
                    $userIds[] = $data->id;
                } elseif (is_array($data) && isset($data['id'])) {
                    $userIds[] = $data['id'];
                }
            }

            return array_values(array_unique($userIds));
        } catch (AblyException $exception) {
            report($exception);

            return [];
        }
    }

    public function isPresent(string $conversationId, string $userId): bool
    {
        return in_array($userId, $this->getPresentUserIds($conversationId), true);
    }

    protected function getAbly(): ?AblyRest
    {
        if ($this->ably !== null) {
            return $this->ably;
        }

        $key = config('broadcasting.connections.ably.key');

        if (blank($key)) {
            return null;
        }

        $this->ably = new AblyRest(['key' => $key]);

        return $this->ably;
    }
}
