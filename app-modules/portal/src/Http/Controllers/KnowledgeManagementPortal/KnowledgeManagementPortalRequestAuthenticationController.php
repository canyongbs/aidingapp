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

namespace AidingApp\Portal\Http\Controllers\KnowledgeManagementPortal;

use AidingApp\Contact\Models\Contact;
use AidingApp\Contact\Models\Organization;
use AidingApp\Portal\Actions\FindOrganizationByEmailDomain;
use AidingApp\Portal\Enums\PortalType;
use AidingApp\Portal\Http\Requests\KnowledgeManagementPortalAuthenticationRequest;
use AidingApp\Portal\Models\PortalAuthentication;
use AidingApp\Portal\Notifications\AuthenticatePortalNotification;
use App\Actions\ResolveEducatableFromEmail;
use App\Http\Controllers\Controller;
use App\Support\AuthenticationCodeRateLimiter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class KnowledgeManagementPortalRequestAuthenticationController extends Controller
{
    public function __invoke(
        KnowledgeManagementPortalAuthenticationRequest $request,
        ResolveEducatableFromEmail $resolveEducatableFromEmail,
        FindOrganizationByEmailDomain $findOrganizationByEmailDomain,
        AuthenticationCodeRateLimiter $rateLimiter,
    ): JsonResponse {
        $email = $request->safe()->email;

        $educatable = $resolveEducatableFromEmail($email);

        if (! $educatable) {
            $organization = $findOrganizationByEmailDomain($email);

            if ($organization) {
                $authenticationUrl = $this->createPortalAuthentication($request, $rateLimiter, organization: $organization);

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

        $authenticationUrl = $this->createPortalAuthentication($request, $rateLimiter, $educatable);

        return response()->json([
            'message' => "We've sent an authentication code to {$email}.",
            'authentication_url' => $authenticationUrl,
        ]);
    }

    protected function createPortalAuthentication(
        KnowledgeManagementPortalAuthenticationRequest $request,
        AuthenticationCodeRateLimiter $rateLimiter,
        ?Contact $contact = null,
        ?Organization $organization = null,
    ): string {
        $email = $request->safe()->email;

        [$rateLimitTarget, $scope] = $contact
            ? [$contact, 'km-portal']
            : [$organization, 'km-portal-register:' . sha1(Str::lower($email))];

        assert($rateLimitTarget instanceof Model);

        $rateLimiter->ensureCanRequestCode($rateLimitTarget, $scope);

        if ($contact) {
            PortalAuthentication::invalidateExistingCodesFor($contact, PortalType::KnowledgeManagement);
        }

        $code = random_int(100000, 999999);

        $authentication = new PortalAuthentication();
        $authentication->portal_type = PortalType::KnowledgeManagement;
        $authentication->code = Hash::make((string) $code);

        if ($contact) {
            $authentication->educatable()->associate($contact);
        }

        $authentication->save();

        Notification::route(
            'mail',
            ! is_null($contact)
                ? [
                    $email => $contact->getAttributeValue($contact::displayNameKey()),
                ]
                : $email
        )
            ->notify(new AuthenticatePortalNotification($authentication, $code));

        $rateLimiter->recordCodeRequest($rateLimitTarget, $scope);

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
