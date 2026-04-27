<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Aiding App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Aiding App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AidingApp\ServiceManagement\Filament\Resources\Advisories;

use AidingApp\ServiceManagement\Filament\Resources\Advisories\Pages\CreateAdvisory;
use AidingApp\ServiceManagement\Filament\Resources\Advisories\Pages\EditAdvisory;
use AidingApp\ServiceManagement\Filament\Resources\Advisories\Pages\ListAdvisories;
use AidingApp\ServiceManagement\Filament\Resources\Advisories\Pages\ManageAdvisoryUpdate;
use AidingApp\ServiceManagement\Filament\Resources\Advisories\Pages\ViewAdvisory;
use AidingApp\ServiceManagement\Models\Advisory;
use App\Enums\Feature;
use App\Features\IncidentRenameFeature;
use BackedEnum;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Gate;
use UnitEnum;

class AdvisoryResource extends Resource
{
    protected static ?string $model = Advisory::class;

    protected static ?string $navigationLabel = 'Advisories';

    protected static ?string $label = 'Advisory';

    protected static ?string $slug = 'advisories';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-m-clipboard-document-list';

    protected static string | UnitEnum | null $navigationGroup = 'Service Management';

    protected static ?int $navigationSort = 60;

    protected static ?string $breadcrumb = 'Advisories';

    public static function getRecordSubNavigation(Page $page): array
    {
        $navigationItems = [
            ViewAdvisory::class,
            EditAdvisory::class,
            ManageAdvisoryUpdate::class,
        ];

        return $page->generateNavigationItems($navigationItems);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAdvisories::route('/'),
            'create' => CreateAdvisory::route('/create'),
            'view' => ViewAdvisory::route('/{record}'),
            'edit' => EditAdvisory::route('/{record}/edit'),
            'manage-advisory-update' => ManageAdvisoryUpdate::route('/{record}/updates'),
        ];
    }

    //TODO: IncidentRenameFeature clean up - remove entire canAccess when you remove the feature flag.
    public static function canAccess(): bool
    {
        return IncidentRenameFeature::active()
          && Gate::check(Feature::AdvisoryManagement->getGateName())
          && auth()->user()->can('advisory.view-any');
    }
}
