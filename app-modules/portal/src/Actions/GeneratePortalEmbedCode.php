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

namespace AidingApp\Portal\Actions;

use AidingApp\Portal\Enums\PortalType;
use AidingApp\Portal\Settings\PortalSettings;
use Exception;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;

class GeneratePortalEmbedCode
{
    public function handle(PortalType $portal): string
    {
        return match ($portal) {
            PortalType::KnowledgeManagement => (function () {
                $loaderScriptUrl = Vite::asset('src/loader.js', 'js/portals/knowledge-management');

                $portalAccessUrl = route('portal.show');

                $userAuthenticationUrl = route('api.user.auth-check');

                $portalDefinitionUrl = URL::to(
                    URL::signedRoute(
                        name: 'api.portal.define',
                        absolute: false,
                    )
                );

                $portalSearchUrl = URL::to(
                    URL::signedRoute(
                        name: 'api.portal.search',
                        absolute: false,
                    )
                );

                $portalResourcesUrl = URL::to(
                    URL::signedRoute(
                        name: 'api.portal.resources',
                        absolute: false,
                    )
                );

                $appUrl = config('app.url');

                $apiUrl = route('api.portal.define');

                $appTitle = app(PortalSettings::class)->page_title;

                $appTitle = $appTitle . ' - ' . config('app.name');

                return <<<EOD
                <knowledge-management-portal-embed app-title="{$appTitle}" url="{$portalDefinitionUrl}" user-authentication-url="{$userAuthenticationUrl}" access-url="{$portalAccessUrl}" search-url="{$portalSearchUrl}" resources-url="{$portalResourcesUrl}" app-url="{$appUrl}" api-url="{$apiUrl}"></knowledge-management-portal-embed>
                <script src="{$loaderScriptUrl}"></script>
                EOD;
            })(),
            default => throw new Exception('Unsupported Portal.'),
        };
    }
}
