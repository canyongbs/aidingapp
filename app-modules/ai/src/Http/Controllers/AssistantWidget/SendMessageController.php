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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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

namespace AidingApp\Ai\Http\Controllers\AssistantWidget;

use AidingApp\Ai\Jobs\PortalAssistant\SendMessage;
use AidingApp\Ai\Models\PortalAssistantThread;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class SendMessageController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->validate([
            'content' => ['required', 'string', 'max:25000'],
            'thread_id' => ['nullable', 'uuid'],
            'guest_token' => ['nullable', 'uuid'],
        ]);

        $author = auth('contact')->user();

        if (filled($data['thread_id'] ?? null)) {
            $thread = PortalAssistantThread::query()
                ->whereKey($data['thread_id'])
                ->firstOrFail();

            if ($author) {
                // Allow if the authenticated user owns the thread
                if ($thread->author_type && $thread->author_id) {
                    if (! $thread->author()->is($author)) {
                        abort(403, 'You do not have access to this thread.');
                    }
                } else {
                    // Guest thread: claim ownership for the now-authenticated user
                    $thread->author()->associate($author);
                    $thread->save();
                }
            } else {
                // Guest access: thread must have no author and matching guest_token
                if ($thread->author_type || $thread->author_id) {
                    abort(403, 'You do not have access to this thread.');
                }

                if (filled($data['guest_token'] ?? null)) {
                    if ($thread->guest_token !== $data['guest_token']) {
                        abort(403, 'Invalid guest token.');
                    }
                } else {
                    abort(403, 'You do not have access to this thread.');
                }
            }
        } else {
            $thread = new PortalAssistantThread();

            if ($author) {
                $thread->author()->associate($author);
            }

            $thread->guest_token = $data['guest_token'] ?? Str::uuid()->toString();

            $thread->save();
        }

        dispatch(new SendMessage(
            $thread,
            $data['content'],
            request: [
                'headers' => Arr::only(
                    request()->headers->all(),
                    ['host', 'sec-ch-ua', 'user-agent', 'sec-ch-ua-platform', 'origin', 'referer', 'accept-language'],
                ),
                'ip' => request()->ip(),
            ],
        ));

        return response()->json([
            'message' => 'Message dispatched for processing via websockets.',
            'thread_id' => $thread->getKey(),
            'guest_token' => $thread->guest_token,
        ]);
    }
}
