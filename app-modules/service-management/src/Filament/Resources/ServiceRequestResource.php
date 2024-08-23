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

namespace AidingApp\ServiceManagement\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Resources\Pages\Page;
use AidingApp\ServiceManagement\Models\ServiceRequest;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestResource\Pages\ManageAssignment;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestResource\Pages\EditServiceRequest;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestResource\Pages\ViewServiceRequest;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestResource\Pages\ListServiceRequests;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestResource\Pages\CreateServiceRequest;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestResource\Pages\ServiceRequestTimeline;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestResource\Pages\ManageServiceRequestUpdate;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestResource\Pages\ManageAssignments;
use AidingApp\ServiceManagement\Filament\Resources\ServiceRequestResource\Pages\ManageServiceRequestFormSubmission;

class ServiceRequestResource extends Resource
{
  protected static ?string $model = ServiceRequest::class;

  protected static ?string $navigationIcon = 'heroicon-o-briefcase';

  protected static ?string $navigationGroup = 'Service Management';

  protected static ?int $navigationSort = 10;

  public static function shouldShowFormSubmission(Page $page): bool
  {
    if (! is_null($page->record) && ! is_null($page->record->serviceRequestFormSubmission)) {
      return true;
    }

    return false;
  }

  public static function getRecordSubNavigation(Page $page): array
  {
    $navigationItems = [
      ViewServiceRequest::class,
      EditServiceRequest::class,
      ManageAssignments::class,
      ManageServiceRequestUpdate::class,
      ServiceRequestTimeline::class,
    ];

    if (static::shouldShowFormSubmission($page)) {
      array_splice($navigationItems, 1, 0, ManageServiceRequestFormSubmission::class);
    }

    return $page->generateNavigationItems($navigationItems);
  }

  public static function getPages(): array
  {
    return [
      'index' => ListServiceRequests::route('/'),
      'manage-assignments' => ManageAssignments::route('/{record}/users'),
      'manage-service-request-updates' => ManageServiceRequestUpdate::route('/{record}/updates'),
      'manage-service-request-form-submission' => ManageServiceRequestFormSubmission::route('/{record}/form-submission'),
      'create' => CreateServiceRequest::route('/create'),
      'view' => ViewServiceRequest::route('/{record}'),
      'edit' => EditServiceRequest::route('/{record}/edit'),
      'timeline' => ServiceRequestTimeline::route('/{record}/timeline'),
    ];
  }
}
