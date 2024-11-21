<?php

namespace AidingApp\ServiceManagement\Policies;

use App\Models\Authenticatable;
use Illuminate\Auth\Access\Response;
use AidingApp\Contact\Models\Contact;
use AidingApp\ServiceManagement\Models\ProductLicense;
use App\Features\LicenseManagement;

class ProductLicensePolicy
{
    public function before(Authenticatable $authenticatable): ?Response
    {
        if (! $authenticatable->hasAnyLicense([Contact::getLicenseType()])) {
            return Response::deny('You are not licensed for the Recruitment CRM.');
        }

        if (! LicenseManagement::active()) {
            return Response::deny('This feature is not active currently.');
        }

        return null;
    }

    public function viewAny(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'product-license.view-any',
            denyResponse: 'You do not have permission to view product license.'
        );
    }

    public function view(Authenticatable $authenticatable, ProductLicense $productLicense): Response
    {
        return $authenticatable->canOrElse(
            abilities: ["product-license.{$productLicense->getKey()}.view"],
            denyResponse: 'You do not have permission to view this product license.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'product-license.create',
            denyResponse: 'You do not have permission to create product license.'
        );
    }

    public function update(Authenticatable $authenticatable, ProductLicense $productLicense): Response
    {
        return $authenticatable->canOrElse(
            abilities: ["product-license.{$productLicense->getKey()}.view"],
            denyResponse: 'You do not have permission to view this product license.'
        );
    }

    public function delete(Authenticatable $authenticatable, ProductLicense $productLicense): Response
    {
        return $authenticatable->canOrElse(
            abilities: ["product-license.{$productLicense->getKey()}.delete"],
            denyResponse: 'You do not have permission to delete this product license.'
        );
    }

    public function restore(Authenticatable $authenticatable, ProductLicense $productLicense): Response
    {
        return $authenticatable->canOrElse(
            abilities: ["product-license.{$productLicense->getKey()}.restore"],
            denyResponse: 'You do not have permission to restore this product license.'
        );
    }

    public function forceDelete(Authenticatable $authenticatable, ProductLicense $productLicense): Response
    {
        return $authenticatable->canOrElse(
            abilities: ["product-license.{$productLicense->getKey()}.force-delete"],
            denyResponse: 'You do not have permission to force delete this product license.'
        );
    }
}
