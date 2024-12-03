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

namespace AidingApp\LicenseManagement\Policies;

use App\Models\Authenticatable;
use Illuminate\Auth\Access\Response;
use AidingApp\Contact\Models\Contact;
use AidingApp\LicenseManagement\Models\ProductLicense;

class ProductLicensePolicy
{
    public function before(Authenticatable $authenticatable): ?Response
    {
        if (! $authenticatable->hasAnyLicense([Contact::getLicenseType()])) {
            return Response::deny('You are not licensed for the Recruitment CRM.');
        }

        return null;
    }

    public function viewAny(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'product_license.view-any',
            denyResponse: 'You do not have permission to view product licenses.'
        );
    }

    public function view(Authenticatable $authenticatable, ProductLicense $productLicense): Response
    {
        return $authenticatable->canOrElse(
            abilities: ["product_license.{$productLicense->getKey()}.view"],
            denyResponse: 'You do not have permission to view this product license.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'product_license.create',
            denyResponse: 'You do not have permission to create product licenses.'
        );
    }

    public function update(Authenticatable $authenticatable, ProductLicense $productLicense): Response
    {
        return $authenticatable->canOrElse(
            abilities: ["product_license.{$productLicense->getKey()}.update"],
            denyResponse: 'You do not have permission to update this product license.'
        );
    }

    public function delete(Authenticatable $authenticatable, ProductLicense $productLicense): Response
    {
        return $authenticatable->canOrElse(
            abilities: ["product_license.{$productLicense->getKey()}.delete"],
            denyResponse: 'You do not have permission to delete this product license.'
        );
    }

    public function restore(Authenticatable $authenticatable, ProductLicense $productLicense): Response
    {
        return $authenticatable->canOrElse(
            abilities: ["product_license.{$productLicense->getKey()}.restore"],
            denyResponse: 'You do not have permission to restore this product license.'
        );
    }

    public function forceDelete(Authenticatable $authenticatable, ProductLicense $productLicense): Response
    {
        return $authenticatable->canOrElse(
            abilities: ["product_license.{$productLicense->getKey()}.force-delete"],
            denyResponse: 'You do not have permission to force delete this product license.'
        );
    }
}
