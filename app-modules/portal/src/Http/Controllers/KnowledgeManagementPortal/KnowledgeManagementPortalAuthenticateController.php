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

namespace AidingApp\Portal\Http\Controllers\KnowledgeManagementPortal;

use AidingApp\Ai\Settings\AiSupportAssistantSettings;
use AidingApp\Contact\Models\Contact;
use AidingApp\Portal\Http\Requests\KnowledgeManagementPortalAuthenticateRequest;
use AidingApp\Portal\Models\PortalAuthentication;
use AidingApp\Portal\Models\PortalGuest;
use AidingApp\Portal\Settings\PortalSettings;
use App\Features\AiFeatureTogglesFeature;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class KnowledgeManagementPortalAuthenticateController extends Controller
{
    public function __invoke(KnowledgeManagementPortalAuthenticateRequest $request, PortalAuthentication $authentication): JsonResponse
    {
        if ($authentication->isExpired()) {
            if (! session()->has('guest_id')) {
                $portalGuest = PortalGuest::create();
                session()->put('guest_id', $portalGuest->getKey());
            }

            return response()->json([
                'is_expired' => true,
            ]);
        }

        if (session()->has('guest_id')) {
            session()->forget('guest_id');
        }
        /** @var Contact $contact */
        $contact = $authentication->educatable;

        Auth::guard('contact')->login($contact);

        $settings = resolve(PortalSettings::class);

        $token = $contact->createToken('knowledge-management-portal-access-token');

        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        $assistantEnabled = AiFeatureTogglesFeature::active() && app(AiSupportAssistantSettings::class)->is_enabled && app(PortalSettings::class)->ai_support_assistant;

        return response()->json([
            'success' => true,
            'token' => $token->plainTextToken,
            'user' => auth('contact')->user(),
            'service_management_enabled' => $settings->knowledge_management_portal_service_management,
            'has_assets' => auth()->guard('contact')->user()?->assetCheckIns()->exists() || auth()->guard('contact')->user()?->assetCheckOuts()->exists() ?: false,
            'has_license' => auth()->guard('contact')->user()?->productLicenses()->exists() ?: false,
            'has_tasks' => auth()->guard('contact')->user()?->tasks()->exists() ?: false,
            'assistant_enabled' => $assistantEnabled,
            'assistant_widget_loader_url' => $assistantEnabled
                ? url('widgets/assistant/' . json_decode(Storage::disk('public')->get('widgets/assistant/.vite/manifest.json'), true)['src/loader.js']['file'])
                : null,
            'assistant_widget_config_url' => $assistantEnabled
                ? route('widgets.assistant.api.config')
                : null,
        ]);
    }
}
