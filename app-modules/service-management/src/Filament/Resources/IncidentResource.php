<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

namespace AidingApp\ServiceManagement\Filament\Resources;

use AidingApp\ServiceManagement\Filament\Resources\IncidentResource\Pages\CreateIncident;
use AidingApp\ServiceManagement\Filament\Resources\IncidentResource\Pages\EditIncident;
use AidingApp\ServiceManagement\Filament\Resources\IncidentResource\Pages\ListIncidents;
use AidingApp\ServiceManagement\Filament\Resources\IncidentResource\Pages\ManageIncidentUpdate;
use AidingApp\ServiceManagement\Filament\Resources\IncidentResource\Pages\ViewIncident;
use AidingApp\ServiceManagement\Models\Incident;
use BackedEnum;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use UnitEnum;

class IncidentResource extends Resource
{
    protected static ?string $model = Incident::class;

    protected static ?string $navigationLabel = 'Incident Management';

    protected static string | BackedEnum | null $navigationIcon = 'heroicon-m-clipboard-document-list';

    protected static string | UnitEnum | null $navigationGroup = 'Service Management';

    protected static ?int $navigationSort = 60;

    protected static ?string $breadcrumb = 'Incident Management';

    public static function getRecordSubNavigation(Page $page): array
    {
        $navigationItems = [
            ViewIncident::class,
            EditIncident::class,
            ManageIncidentUpdate::class,
        ];

        return $page->generateNavigationItems($navigationItems);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListIncidents::route('/'),
            'create' => CreateIncident::route('/create'),
            'view' => ViewIncident::route('/{record}'),
            'edit' => EditIncident::route('/{record}/edit'),
            'manage-incident-update' => ManageIncidentUpdate::route('/{record}/updates'),
        ];
    }
}
