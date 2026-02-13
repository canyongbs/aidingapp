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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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

namespace AidingApp\Ai\Http\Controllers\AssistantWidget;

use AidingApp\Ai\Models\PortalAssistantThread;
use AidingApp\Contact\Models\Contact;
use App\Features\EmbeddableSupportAssistantFeature;
use Illuminate\Contracts\Broadcasting\Broadcaster;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AssistantBroadcastController extends Controller
{
    public function auth(Request $request, Broadcaster $broadcaster): mixed
    {
        if ($request->hasSession()) {
            $request->session()->reflash();
        }

        $channelName = $request->channel_name;

        if (empty($channelName)) {
            throw new AccessDeniedHttpException();
        }

        $normalizedName = $broadcaster->normalizeChannelName($channelName);

        if (! Str::startsWith($normalizedName, 'portal-assistant-thread-')) {
            throw new AccessDeniedHttpException();
        }

        $threadId = Str::after($normalizedName, 'portal-assistant-thread-');
        $thread = PortalAssistantThread::find($threadId);

        if (! $thread) {
            throw new AccessDeniedHttpException();
        }

        $user = Auth::guard('contact')->user();

        if ($user instanceof Contact && $thread->author()->is($user)) {
            return $broadcaster->validAuthenticationResponse($request, true);
        }

        if (
            ! $user
            && ! $thread->author_type
            && ! $thread->author_id
            && EmbeddableSupportAssistantFeature::active()
        ) {
            $guestToken = $request->input('guest_token');

            if ($guestToken && $thread->guest_token === $guestToken) {
                return $broadcaster->validAuthenticationResponse($request, true);
            }
        }

        throw new AccessDeniedHttpException();
    }
}
