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
use AidingApp\Portal\Models\PortalGuest;
use AidingApp\Portal\Settings\PortalSettings;
use App\Features\AiFeatureTogglesFeature;
use App\Http\Controllers\Controller;
use App\Settings\LicenseSettings;
use Filament\Support\Colors\Color;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;

class KnowledgeManagementPortalController extends Controller
{
    public function show(): JsonResponse
    {
        $settings = resolve(PortalSettings::class);
        $logo = $settings->getSettingsPropertyModel('portal.logo')
            ->getFirstMedia('logo');
        $favicon = $settings->getSettingsPropertyModel('portal.favicon')
            ->getFirstMedia('portal_favicon');

        if (! auth()->guard('contact')->check() && ! session()->has('guest_id')) {
            $portalGuest = PortalGuest::create();
            session()->put('guest_id', $portalGuest->getKey());
        }

        return response()->json([
            'header_logo' => $logo ? $logo->getTemporaryUrl(
                expiration: now()->addMinutes(5),
            ) : url(Vite::asset('resources/images/default-logo-light-1735308866.svg')),
            'favicon' => $favicon?->getTemporaryUrl(
                expiration: now()->addMinutes(5),
                conversionName: 'portal_favicon',
            ),
            'app_name' => config('app.name'),
            'primary_color' => collect(Color::all()[$settings->knowledge_management_portal_primary_color->value ?? 'blue'])
                ->map(Color::convertToRgb(...))
                ->map(fn (string $value): string => (string) str($value)->after('rgb(')->before(')'))
                ->all(),
            'rounding' => $settings->knowledge_management_portal_rounding,
            'requires_authentication' => $settings->knowledge_management_portal_requires_authentication,
            'service_management_enabled' => $settings->knowledge_management_portal_service_management && resolve(LicenseSettings::class)->data?->addons?->serviceManagement,
            'has_assets' => auth()->guard('contact')->user()?->assetCheckIns()->exists() || auth()->guard('contact')->user()?->assetCheckOuts()->exists() ?: false,
            'has_license' => auth()->guard('contact')->user()?->productLicenses()->exists() ?: false,
            'has_tasks' => auth()->guard('contact')->user()?->tasks()->exists() ?: false,
            'authentication_url' => URL::to(
                URL::signedRoute(
                    name: 'api.portal.request-authentication',
                    absolute: false,
                )
            ),
            'footer_logo' => Vite::asset('resources/svg/CGBS_Logo_FullColor_Light.svg'),
            'assistant_widget_loader_url' => (AiFeatureTogglesFeature::active() && app(AiSupportAssistantSettings::class)->is_enabled && app(PortalSettings::class)->ai_support_assistant && auth()->guard('contact')->user()) 
                ? url('widgets/assistant/' . json_decode(Storage::disk('public')->get('widgets/assistant/.vite/manifest.json'), true)['src/loader.js']['file'])
                : null,
            'assistant_widget_config_url' => (AiFeatureTogglesFeature::active() && app(AiSupportAssistantSettings::class)->is_enabled && app(PortalSettings::class)->ai_support_assistant && auth()->guard('contact')->user())
                ? route('widgets.assistant.api.config')
                : null,
        ]);
    }
}
