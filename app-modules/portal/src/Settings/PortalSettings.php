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

namespace AidingApp\Portal\Settings;

use Spatie\LaravelSettings\Settings;

class PortalSettings extends Settings
{
    public ?string $footer_color = null;

    public ?string $footer_copyright_statement;

    /**
    * Knowledge Base Portal
    */
    public bool $knowledge_management_portal_enabled = false;

    public bool $knowledge_management_portal_service_management = false;

    public bool $knowledge_management_portal_requires_authentication = false;

    public ?string $knowledge_management_portal_primary_color = null;

    public ?string $knowledge_management_portal_rounding = null;

    public ?string $knowledge_management_portal_authorized_domain = null;

    public static function group(): string
    {
        return 'portal';
    }
}
