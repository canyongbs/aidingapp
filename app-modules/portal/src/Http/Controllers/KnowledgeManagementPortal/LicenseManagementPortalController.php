<?php

namespace AidingApp\Portal\Http\Controllers\KnowledgeManagementPortal;

use AidingApp\LicenseManagement\Enums\ProductLicenseStatus;
use AidingApp\LicenseManagement\Models\ProductLicense;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LicenseManagementPortalController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $licenses = auth('contact')->user()->productLicenses()
            ->withWhereHas('product:id,name,version')
            ->get()
            ->map(function ($license) {
                /** @var ProductLicense $license */
                $license->formatted_expiration_date = $license->expiration_date?->format('m-d-Y');

                return $license;
            });

        return response()->json([
            'success' => true,
            'activeLicense' => $licenses->filter(
                fn (ProductLicense $license): bool => $license->status === ProductLicenseStatus::Active
            )->values(),
            'expiredLicense' => $licenses->filter(
                fn (ProductLicense $license): bool => $license->status === ProductLicenseStatus::Expired
            )->values(),
        ]);
    }
}
