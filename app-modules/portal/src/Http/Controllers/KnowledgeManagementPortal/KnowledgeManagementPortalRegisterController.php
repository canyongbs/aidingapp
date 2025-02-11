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

namespace AidingApp\Portal\Http\Controllers\KnowledgeManagementPortal;

use AidingApp\Contact\Enums\SystemContactClassification;
use AidingApp\Contact\Models\Contact;
use AidingApp\Contact\Models\ContactSource;
use AidingApp\Contact\Models\ContactStatus;
use AidingApp\Portal\Http\Requests\KnowledgeManagementPortalRegisterRequest;
use AidingApp\Portal\Models\PortalAuthentication;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class KnowledgeManagementPortalRegisterController extends Controller
{
    public function __invoke(KnowledgeManagementPortalRegisterRequest $request, PortalAuthentication $authentication): JsonResponse
    {
        if ($authentication->isExpired()) {
            return response()->json([
                'is_expired' => true,
            ]);
        }

        $data = $request->validated();

        /** @var Contact $contact */
        $contact = Contact::query()
            ->make([
                'email' => $data['email'],
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'full_name' => "{$data['first_name']} {$data['last_name']}",
                'preferred' => $data['preferred'] ?? null,
                'mobile' => $data['mobile'],
                'phone' => $data['phone'] ?? null,
                'sms_opt_out' => $data['sms_opt_out'],
            ]);

        $status = ContactStatus::query()
            ->where('classification', SystemContactClassification::New)
            ->first();

        if ($status) {
            $contact->status()->associate($status);
        }

        $source = ContactSource::query()
            ->where('name', 'Portal Generated')
            ->first();

        if (! $source) {
            $source = ContactSource::query()
                ->create([
                    'name' => 'Portal Generated',
                ]);
        }

        $contact->source()->associate($source);

        $contact->save();

        Auth::guard('contact')->login($contact);

        $token = $contact->createToken('knowledge-management-portal-access-token');

        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        return response()->json([
            'success' => true,
            'token' => $token->plainTextToken,
            'user' => auth('contact')->user(),
        ]);
    }
}
