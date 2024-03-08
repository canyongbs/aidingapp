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

namespace AidingApp\Portal\Http\Controllers\KnowledgeManagementPortal;

use App\Settings\LicenseSettings;
use Illuminate\Http\JsonResponse;
use Filament\Support\Colors\Color;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Filament\Support\Colors\ColorManager;
use AidingApp\Portal\Settings\PortalSettings;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\KnowledgeBase\Models\KnowledgeBaseCategory;
use AidingApp\Portal\DataTransferObjects\KnowledgeBaseCategoryData;

class KnowledgeManagementPortalController extends Controller
{
    public function show(): JsonResponse
    {
        $settings = resolve(PortalSettings::class);
        $license = resolve(LicenseSettings::class);

        $colors = [
            ...app(ColorManager::class)->getColors(),
            ...Color::all(),
        ];

        return response()->json([
            'primary_color' => Color::all()[$settings->knowledge_management_portal_primary_color ?? 'blue'],
            'rounding' => $settings->knowledge_management_portal_rounding,
            'authentication_url' => URL::to(
                URL::signedRoute(
                    name: 'api.portal.knowledge-management.request-authentication',
                    absolute: false,
                )
            ),
            'categories' => KnowledgeBaseCategoryData::collection(
                KnowledgeBaseCategory::query()
                    ->orderBy('name')
                    ->get()
                    ->map(function (KnowledgeBaseCategory $category) {
                        return [
                            'id' => $category->getKey(),
                            'name' => $category->name,
                            'description' => $category->description,
                            'icon' => $category->icon ? svg($category->icon, 'h-6 w-6')->toHtml() : null,
                        ];
                    })
                    ->toArray()
            ),
            'service_requests' => $license->data->addons->serviceManagement && $settings->knowledge_management_portal_service_management && auth('contact')->check()
                ? auth('contact')->user()
                    ->serviceRequests()
                    ->get()
                    ->map(function (ServiceRequest $serviceRequest) use ($colors) {
                        return [
                            'id' => $serviceRequest->getKey(),
                            'title' => $serviceRequest->title,
                            'status_name' => $serviceRequest->status?->name,
                            // 'status_color' => $colors[$serviceRequest->status?->color->value][600],
                            'icon' => $serviceRequest->priority->type->icon ? svg($serviceRequest->priority->type->icon, 'h-6 w-6')->toHtml() : null,
                            // 'updated_at' => $serviceRequest->serviceRequestUpdates()?->latest('updated_at')->first()->updated_at->format('n-j-y g:i A'),
                        ];
                    })
                : [],
        ]);
    }
}
