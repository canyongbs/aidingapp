<?php

namespace AidingApp\ServiceManagement\Policies;

use App\Models\Authenticatable;
use App\Features\LicenseManagement;
use Illuminate\Auth\Access\Response;
use AidingApp\Contact\Models\Contact;
use AidingApp\ServiceManagement\Models\Product;

class ProductPolicy
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
            abilities: 'product.view-any',
            denyResponse: 'You do not have permission to view product.'
        );
    }

    public function view(Authenticatable $authenticatable, Product $product): Response
    {
        return $authenticatable->canOrElse(
            abilities: ["product.{$product->getKey()}.view"],
            denyResponse: 'You do not have permission to view this product.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'product.create',
            denyResponse: 'You do not have permission to create product.'
        );
    }

    public function update(Authenticatable $authenticatable, Product $product): Response
    {
        return $authenticatable->canOrElse(
            abilities: ["product.{$product->getKey()}.view"],
            denyResponse: 'You do not have permission to view this product.'
        );
    }

    public function delete(Authenticatable $authenticatable, Product $product): Response
    {
        return $authenticatable->canOrElse(
            abilities: ["product.{$product->getKey()}.delete"],
            denyResponse: 'You do not have permission to delete this product.'
        );
    }

    public function restore(Authenticatable $authenticatable, Product $product): Response
    {
        return $authenticatable->canOrElse(
            abilities: ["product.{$product->getKey()}.restore"],
            denyResponse: 'You do not have permission to restore this product.'
        );
    }

    public function forceDelete(Authenticatable $authenticatable, Product $product): Response
    {
        return $authenticatable->canOrElse(
            abilities: ["product.{$product->getKey()}.force-delete"],
            denyResponse: 'You do not have permission to force delete this product.'
        );
    }
}
