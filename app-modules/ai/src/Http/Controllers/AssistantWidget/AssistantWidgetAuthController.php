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

namespace AidingApp\Ai\Http\Controllers\AssistantWidget;

use AidingApp\Contact\Models\Contact;
use AidingApp\Portal\Enums\PortalType;
use AidingApp\Portal\Models\PortalAuthentication;
use AidingApp\Portal\Notifications\AuthenticatePortalNotification;
use AidingApp\Portal\Rules\PortalAuthenticateCodeValidation;
use App\Actions\ResolveEducatableFromEmail;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;

class AssistantWidgetAuthController extends Controller
{
    public function request(Request $request, ResolveEducatableFromEmail $resolveEducatableFromEmail): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $contact = $resolveEducatableFromEmail($data['email']);

        if (! $contact) {
            throw ValidationException::withMessages([
                'email' => 'A contact with that email address could not be found.',
            ]);
        }

        $code = random_int(100000, 999999);

        $authentication = new PortalAuthentication();
        $authentication->portal_type = PortalType::KnowledgeManagement;
        $authentication->code = Hash::make((string) $code);
        $authentication->educatable()->associate($contact);
        $authentication->save();

        Notification::route('mail', [
            $data['email'] => $contact->getAttributeValue($contact::displayNameKey()),
        ])->notify(new AuthenticatePortalNotification($authentication, $code));

        $authenticationUrl = URL::to(
            URL::signedRoute(
                name: 'widgets.assistant.api.authenticate',
                parameters: ['authentication' => $authentication],
                absolute: false,
            )
        );

        return response()->json([
            'message' => "We've sent an authentication code to {$data['email']}.",
            'authentication_url' => $authenticationUrl,
        ]);
    }

    public function authenticate(Request $request, PortalAuthentication $authentication): JsonResponse
    {
        if ($authentication->isExpired()) {
            return response()->json(['is_expired' => true], 422);
        }

        $request->validate([
            'code' => ['required', 'integer', 'digits:6', new PortalAuthenticateCodeValidation()],
        ]);

        /** @var Contact $contact */
        $contact = $authentication->educatable;

        Auth::guard('contact')->login($contact);

        $token = $contact->createToken('knowledge-management-portal-access-token');

        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        return response()->json([
            'success' => true,
            'token' => $token->plainTextToken,
        ]);
    }
}
