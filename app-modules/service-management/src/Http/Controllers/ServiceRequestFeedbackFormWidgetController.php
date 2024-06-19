<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

use Closure;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\SettingsProperty;
use Illuminate\Http\JsonResponse;
use Filament\Support\Colors\Color;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Vite;
use AidingApp\Portal\Enums\PortalType;
use Illuminate\Support\Facades\Validator;
use AidingApp\Theme\Settings\ThemeSettings;
use App\Actions\ResolveEducatableFromEmail;
use Illuminate\Support\Facades\Notification;
use AidingApp\Portal\Settings\PortalSettings;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use AidingApp\Portal\Models\PortalAuthentication;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\Portal\Notifications\AuthenticatePortalNotification;

class ServiceRequestFeedbackFormWidgetController extends Controller
{
    public function view(Request $request, ServiceRequest $serviceRequest): JsonResponse
    {
        $settingsProperty = SettingsProperty::getInstance('theme.is_logo_active');
        $logo = $settingsProperty->getFirstMedia('logo');

        return response()->json(
            [
                'is_authenticated' => (bool) $request->user(),
                ...($request->user() ? [
                    'authentication_url' => URL::signedRoute(
                        name: 'service-requests.feedback.request-authentication',
                        parameters: ['serviceRequest' => $serviceRequest],
                        absolute: false
                    ),
                ] : [
                    'submission_url' => URL::signedRoute(
                        name: 'service-requests.feedback.submit',
                        parameters: ['serviceRequest' => $serviceRequest],
                        absolute: false
                    ),
                ]),
                'header_logo' => $logo?->getTemporaryUrl(
                    expiration: now()->addMinutes(5),
                    conversionName: 'logo-height-250px',
                ),
                'app_name' => config('app.name'),
                'has_enabled_csat' => $serviceRequest?->priority?->type?->has_enabled_csat,
                'has_enabled_nps' => $serviceRequest?->priority?->type?->has_enabled_nps,
                // 'recaptcha_enabled' => $serviceRequestForm->recaptcha_enabled,
                // ...($serviceRequestForm->recaptcha_enabled ? [
                //     'recaptcha_site_key' => app(GoogleRecaptchaSettings::class)->site_key,
                // ] : []),
                'footer_logo' => Vite::asset('resources/images/canyon-logo-light.png'),
                'primary_color' => Color::all()[app(PortalSettings::class)->knowledge_management_portal_primary_color ?? 'blue'],
                'rounding' => app(PortalSettings::class)->knowledge_management_portal_rounding,
            ],
        );
    }

    public function requestAuthentication(Request $request, ResolveEducatableFromEmail $resolveEducatableFromEmail, ServiceRequest $serviceRequest): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $educatable = $resolveEducatableFromEmail($data['email']);

        if (! $educatable) {
            throw ValidationException::withMessages([
                'email' => 'A contact with that email address could not be found. Please contact your system administrator.',
            ]);
        }

        $code = random_int(100000, 999999);

        $authentication = new PortalAuthentication();
        $authentication->educatable()->associate($educatable);
        $authentication->portal_type = PortalType::KnowledgeManagement;
        $authentication->code = Hash::make($code);
        $authentication->save();

        Notification::route('mail', [
            $data['email'] => $educatable->getAttributeValue($educatable::displayNameKey()),
        ])->notify(new AuthenticatePortalNotification($authentication, $code));

        return response()->json([
            'message' => "We've sent an authentication code to {$data['email']}.",
            'authentication_url' => URL::signedRoute(
                name: 'service-requests.feedback.authenticate',
                parameters: [
                    'serviceRequest' => $serviceRequest,
                    'authentication' => $authentication,
                ],
                absolute: false
            ),
        ]);
    }

    public function authenticate(Request $request, ServiceRequest $serviceRequest, PortalAuthentication $authentication): JsonResponse
    {
        if ($authentication->isExpired()) {
            return response()->json([
                'is_expired' => true,
            ]);
        }

        $request->validate([
            'code' => ['required', 'integer', 'digits:6', function (string $attribute, int $value, Closure $fail) use ($authentication) {
                if (Hash::check($value, $authentication->code)) {
                    return;
                }

                $fail('The provided code is invalid.');
            }],
        ]);

        return response()->json([
            'submission_url' => URL::signedRoute(
                name: 'service-requests.feedback.submit',
                parameters: [
                    'authentication' => $authentication,
                    'serviceRequest' => $serviceRequest,
                ],
                absolute: false
            ),
        ]);
    }

    public function store(
        Request $request,
        ServiceRequest $serviceRequest,
    ): JsonResponse {
        $authentication = $request->query('authentication');

        if ($authentication) {
            $authentication = PortalAuthentication::findOrFail($authentication);
        }

        if (
            $authentication?->isExpired() ?? true
        ) {
            abort(Response::HTTP_UNAUTHORIZED);
        }

        $validator = Validator::make(
            $request->all(),
            [
                'csat' => ['between:1,5', Rule::requiredIf($serviceRequest?->priority?->type?->has_enabled_csat)],
                'nps' => ['between:1,5', Rule::requiredIf($serviceRequest?->priority?->type?->has_enabled_nps)],
            ]
        );

        if ($validator->fails()) {
            return response()->json(
                [
                    'errors' => (object) $validator->errors(),
                ],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $data = $validator->validated();

        $feedback = $serviceRequest->feedback()->make([
            'csat_answer' => $data['csat'] ?? null,
            'nps_answer' => $data['nps'] ?? null,
        ]);

        $feedback->contact()->associate($authentication->educatable);

        $feedback->save();

        return response()->json(
            [
                'message' => 'Service Request feedback submitted successfully.',
            ]
        );
    }
}
