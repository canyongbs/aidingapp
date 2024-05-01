<?php

namespace AidingApp\Portal\Http\Controllers\KnowledgeManagementPortal;

use App\Settings\LicenseSettings;
use Illuminate\Http\JsonResponse;
use Filament\Support\Colors\Color;
use App\Http\Controllers\Controller;
use Filament\Support\Colors\ColorManager;
use AidingApp\Portal\Settings\PortalSettings;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\Portal\DataTransferObjects\ServiceRequestData;

class GetServiceRequestsController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $license = resolve(LicenseSettings::class);
        $settings = resolve(PortalSettings::class);
        $contact = auth('contact')->user();

        $enabled = $license->data->addons->serviceManagement
            && $settings->knowledge_management_portal_service_management
            && $contact;

        if (! $enabled) {
            return response()->json();
        }

        $colors = [
            ...app(ColorManager::class)->getColors(),
            ...Color::all(),
        ];

        return response()->json(
            ServiceRequestData::collection(
                $contact->serviceRequests()
                    ->with('serviceRequestFormSubmission')
                    ->latest()
                    ->get()
                    ->map(function (ServiceRequest $serviceRequest) use ($colors) {
                        return ServiceRequestData::from([
                            'id' => $serviceRequest->getKey(),
                            'title' => $serviceRequest->serviceRequestFormSubmission?->description ?? $serviceRequest->title,
                            'statusName' => $serviceRequest->status?->name,
                            'statusColor' => $serviceRequest->status ? $colors[$serviceRequest->status->color->value][600] : null,
                            'icon' => $serviceRequest->priority->type->icon ? svg($serviceRequest->priority->type->icon, 'h-6 w-6')->toHtml() : null,
                            'updatedAt' => count($serviceRequest->serviceRequestUpdates) > 0 ? $serviceRequest->serviceRequestUpdates()->latest('updated_at')->first()->updated_at->format('n-j-y g:i A') : $serviceRequest->created_at->format('n-j-y g:i A'),
                        ]);
                    })
                    ->toArray()
            )
        );
    }
}
