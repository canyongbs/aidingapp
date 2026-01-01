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

use AidingApp\Contact\Models\Contact;
use AidingApp\Contact\Models\Organization;
use AidingApp\Portal\Enums\PortalType;
use AidingApp\Portal\Http\Requests\KnowledgeManagementPortalAuthenticationRequest;
use AidingApp\Portal\Models\PortalAuthentication;
use AidingApp\Portal\Notifications\AuthenticatePortalNotification;
use App\Actions\ResolveEducatableFromEmail;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;

class KnowledgeManagementPortalRequestAuthenticationController extends Controller
{
    public function __invoke(KnowledgeManagementPortalAuthenticationRequest $request, ResolveEducatableFromEmail $resolveEducatableFromEmail): JsonResponse
    {
        $email = $request->safe()->email;

        $educatable = $resolveEducatableFromEmail($email);

        if (! $educatable) {
            preg_match('/@([a-zA-Z0-9.-]+\.[a-zA-Z]{2,})$/', $email, $matches);

            $domain = $matches[1];

            $organization = Organization::query()
                ->whereRaw(
                    "EXISTS (
                        SELECT 1
                        FROM jsonb_array_elements(domains) AS elem
                        WHERE LOWER(elem->>'domain') = ?
                    )",
                    [strtolower($domain)]
                )
                ->where('is_contact_generation_enabled', true)
                ->first();

            if ($organization) {
                $authenticationUrl = $this->createPortalAuthentication($request);

                return response()->json([
                    'registrationAllowed' => true,
                    'message' => "We've sent an authentication code to {$email}.",
                    'authentication_url' => $authenticationUrl,
                ], 404);
            }

            throw ValidationException::withMessages([
                'email' => 'A contact with that email address could not be found. Please contact your system administrator.',
            ]);
        }

        $authenticationUrl = $this->createPortalAuthentication($request, $educatable);

        return response()->json([
            'message' => "We've sent an authentication code to {$email}.",
            'authentication_url' => $authenticationUrl,
        ]);
    }

    protected function createPortalAuthentication(KnowledgeManagementPortalAuthenticationRequest $request, ?Contact $contact = null): string
    {
        $code = random_int(100000, 999999);

        $authentication = new PortalAuthentication();
        $authentication->portal_type = PortalType::KnowledgeManagement;
        $authentication->code = Hash::make($code);

        if ($contact) {
            $authentication->educatable()->associate($contact);
        }

        $authentication->save();

        Notification::route(
            'mail',
            ! is_null($contact)
                ? [
                    $request->safe()->email => $contact->getAttributeValue($contact::displayNameKey()),
                ]
                : $request->safe()->email
        )
            ->notify(new AuthenticatePortalNotification($authentication, $code));

        $route = (! is_null($contact))
            ? (
                ($request->safe()->isSpa)
                    ? 'portal.authenticate'
                    : 'api.portal.authenticate.embedded'
            )
            : (
                ($request->safe()->isSpa)
                    ? 'portal.register'
                    : 'api.portal.authenticate.register.embedded'
            );

        return URL::to(
            URL::signedRoute(
                name: $route,
                parameters: [
                    'authentication' => $authentication,
                ],
                absolute: false,
            )
        );
    }
}
