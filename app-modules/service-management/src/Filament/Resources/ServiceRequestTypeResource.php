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

namespace AidingApp\ServiceManagement\Filament\Resources;

use AidingApp\ServiceManagement\Enums\ServiceRequestEmailTemplateType;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestTypeResource\Pages\CreateServiceRequestType;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestTypeResource\Pages\EditServiceRequestType;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestTypeResource\Pages\EditServiceRequestTypeAssignments;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestTypeResource\Pages\EditServiceRequestTypeNotifications;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestTypeResource\Pages\ListServiceRequestTypes;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestTypeResource\Pages\ManageServiceRequestTypeAuditors;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestTypeResource\Pages\ManageServiceRequestTypeManagers;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestTypeResource\Pages\ServiceRequestTypeEmailTemplatePage;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestTypeResource\Pages\ViewServiceRequestType;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestTypeResource\RelationManagers\ServiceRequestPrioritiesRelationManager;
use AidingApp\ServiceManagement\Models\ServiceRequestType;
use App\Filament\Clusters\ServiceManagementAdministration;
use Filament\Navigation\NavigationItem;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ServiceRequestTypeResource extends Resource
{
    protected static ?string $model = ServiceRequestType::class;

    protected static ?string $navigationGroup = 'Service Requests';

    protected static ?string $navigationLabel = 'Types';

    protected static ?int $navigationSort = 10;

    protected static ?string $cluster = ServiceManagementAdministration::class;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ServiceRequestPrioritiesRelationManager::class,
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return [
            ...$page->generateNavigationItems([
                ViewServiceRequestType::class,
                EditServiceRequestType::class,
                ManageServiceRequestTypeManagers::class,
                ManageServiceRequestTypeAuditors::class,
                EditServiceRequestTypeAssignments::class,
                EditServiceRequestTypeNotifications::class,
            ]),
            ...(array_map(
                fn (ServiceRequestEmailTemplateType $type): NavigationItem => Arr::first(ServiceRequestTypeEmailTemplatePage::getNavigationItems(['record' => $page->record, 'type' => $type]))
                    ->label($type->getLabel())
                    ->isActiveWhen(fn (): bool => Str::endsWith(request()->path(), $type)),
                ServiceRequestEmailTemplateType::cases(),
            )),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListServiceRequestTypes::route('/'),
            'create' => CreateServiceRequestType::route('/create'),
            'view' => ViewServiceRequestType::route('/{record}'),
            'edit' => EditServiceRequestType::route('/{record}/edit'),
            'service-request-type-managers' => ManageServiceRequestTypeManagers::route('/{record}/managers'),
            'service-request-type-auditors' => ManageServiceRequestTypeAuditors::route('/{record}/auditors'),
            'service-request-type-assignments' => EditServiceRequestTypeAssignments::route('/{record}/assignments'),
            'service-request-type-notifications' => EditServiceRequestTypeNotifications::route('/{record}/notifications'),
            'service-request-type-email-template' => ServiceRequestTypeEmailTemplatePage::route('/{record}/email-template/{type}'),
        ];
    }
}
