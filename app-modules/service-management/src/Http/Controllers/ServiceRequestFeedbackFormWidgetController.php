<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

namespace AidingApp\ServiceManagement\Http\Controllers;

use AidingApp\Portal\Settings\PortalSettings;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Models\ServiceRequestFeedback;
use AidingApp\Theme\Settings\ThemeSettings;
use App\Http\Controllers\Controller;
use Filament\Support\Colors\Color;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Vite;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class ServiceRequestFeedbackFormWidgetController extends Controller
{
    public function assets(Request $request, ServiceRequest $serviceRequest): JsonResponse
    {
        // Read the Vite manifest to determine the correct asset paths
        $manifestPath = public_path('storage/widgets/service-requests/feedback/.vite/manifest.json');
        /** @var array<string, array{file: string, name: string, src: string, isEntry: bool}> $manifest */
        $manifest = json_decode(File::get($manifestPath), true, 512, JSON_THROW_ON_ERROR);

        $widgetEntry = $manifest['src/widget.js'];

        return response()->json([
            'asset_url' => route('widgets.service-requests.feedback.asset'),
            'entry' => route('widgets.service-requests.feedback.api.entry', ['serviceRequest' => $serviceRequest]),
            'js' => route('widgets.service-requests.feedback.asset', ['file' => $widgetEntry['file']]),
        ]);
    }

    public function view(Request $request, ServiceRequest $serviceRequest): JsonResponse
    {
        $logo = ThemeSettings::getSettingsPropertyModel('theme.is_logo_active')->getFirstMedia('logo');
        $portalSettings = app(PortalSettings::class);

        return response()->json(
            [
                'requires_authentication' => true,
                'is_authenticated' => (bool) $request->user(),
                'authentication_url' => URL::to(
                    URL::signedRoute(
                        name: 'api.portal.request-authentication',
                        absolute: false,
                    )
                ),
                'submission_url' => URL::signedRoute(
                    name: 'service-requests.feedback.submit',
                    parameters: ['serviceRequest' => $serviceRequest],
                    absolute: false
                ),
                'header_logo' => $logo?->getTemporaryUrl(
                    expiration: now()->addMinutes(5),
                    conversionName: 'logo-height-250px',
                ),
                'feedback_submitted' => $serviceRequest?->feedback()->exists() ? true : false,
                'app_name' => config('app.name'),
                'has_enabled_csat' => $serviceRequest->priority?->type?->has_enabled_csat,
                'has_enabled_nps' => $serviceRequest->priority?->type?->has_enabled_nps,
                'footer_logo' => Vite::asset('resources/svg/CGBS_Logo_FullColor_Light.svg'),
                'primary_color' => Color::all()[$portalSettings->knowledge_management_portal_primary_color ?? 'blue'],
                'rounding' => $portalSettings->knowledge_management_portal_rounding,
                'service_request_title' => $serviceRequest->title,
            ],
        );
    }

    public function store(
        Request $request,
        ServiceRequest $serviceRequest,
    ): JsonResponse {
        $contact = auth('contact')->user();

        abort_if(is_null($contact), Response::HTTP_UNAUTHORIZED);

        $validator = Validator::make($request->all(), [
            'csat' => [Rule::requiredIf($serviceRequest?->priority?->type?->has_enabled_csat), 'between:1,5'],
            'nps' => [Rule::requiredIf($serviceRequest?->priority?->type?->has_enabled_nps), 'between:1,5'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => (object) $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = $validator->validated();

        /** @var ServiceRequestFeedback $feedback */
        $feedback = $serviceRequest->feedback()->make([
            'csat_answer' => $data['csat'] ?? null,
            'nps_answer' => $data['nps'] ?? null,
        ]);

        $feedback->contact()->associate($contact);

        $feedback->save();

        return response()->json([
            'message' => 'Service Request feedback submitted successfully.',
        ]);
    }
}
