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

namespace AdvisingApp\Prospect\Filament\Pages;

use App\Models\User;
use AdvisingApp\Prospect\Models\Prospect;
use App\Filament\Widgets\ProspectGrowthChart;
use Filament\Pages\Dashboard as BaseDashboard;
use Symfony\Component\HttpFoundation\Response;
use AdvisingApp\Prospect\Filament\Widgets\ProspectStats;
use AdvisingApp\Prospect\Filament\Widgets\ProspectTasks;

class RecruitmentCrmDashboard extends BaseDashboard
{
    protected static ?string $navigationGroup = 'Recruitment CRM';

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?string $title = 'Recruitment CRM Dashboard';

    protected static string $routePath = 'recruitment-crm-dashboard';

    public static function shouldRegisterNavigation(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return $user->hasLicense(Prospect::getLicenseType());
    }

    public function mount(): void
    {
        /** @var User $user */
        $user = auth()->user();

        abort_unless($user->hasLicense(Prospect::getLicenseType()), Response::HTTP_FORBIDDEN);
    }

    public function getWidgets(): array
    {
        return [
            ProspectStats::class,
            ProspectGrowthChart::class,
            ProspectTasks::class,
        ];
    }
}
