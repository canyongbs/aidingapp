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

namespace AidingApp\Contact\Filament\Resources;

use AidingApp\Contact\Filament\Resources\ContactResource\Pages\AssetManagement;
use AidingApp\Contact\Filament\Resources\ContactResource\Pages\ContactEngagementTimeline;
use AidingApp\Contact\Filament\Resources\ContactResource\Pages\ContactServiceManagement;
use AidingApp\Contact\Filament\Resources\ContactResource\Pages\CreateContact;
use AidingApp\Contact\Filament\Resources\ContactResource\Pages\EditContact;
use AidingApp\Contact\Filament\Resources\ContactResource\Pages\ListContacts;
use AidingApp\Contact\Filament\Resources\ContactResource\Pages\ManageContactAlerts;
use AidingApp\Contact\Filament\Resources\ContactResource\Pages\ManageContactEngagement;
use AidingApp\Contact\Filament\Resources\ContactResource\Pages\ManageContactFiles;
use AidingApp\Contact\Filament\Resources\ContactResource\Pages\ManageContactTasks;
use AidingApp\Contact\Filament\Resources\ContactResource\Pages\ViewContact;
use AidingApp\Contact\Models\Contact;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;

class ContactResource extends Resource
{
    protected static ?string $model = Contact::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Clients';

    protected static ?int $navigationSort = 20;

    protected static ?string $recordTitleAttribute = 'full_name';

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewContact::class,
            EditContact::class,
            ContactServiceManagement::class,
            AssetManagement::class,
            ManageContactFiles::class,
            ManageContactEngagement::class,
            ManageContactAlerts::class,
            ManageContactTasks::class,
            ContactEngagementTimeline::class,
        ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['full_name', 'email', 'mobile', 'phone'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return array_filter([
            'Other ID' => $record->otherid,
            'Email Address' => collect([$record->email, $record->email_id])->filter()->implode(', '),
            'Phone' => collect([$record->mobile, $record->phone])->filter()->implode(', '),
        ], fn (mixed $value): bool => filled($value));
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContacts::route('/'),
            'create' => CreateContact::route('/create'),
            'edit' => EditContact::route('/{record}/edit'),
            'manage-alerts' => ManageContactAlerts::route('/{record}/alerts'),
            'manage-engagement' => ManageContactEngagement::route('/{record}/engagement'),
            'manage-files' => ManageContactFiles::route('/{record}/files'),
            'manage-tasks' => ManageContactTasks::route('/{record}/tasks'),
            'view' => ViewContact::route('/{record}'),
            'timeline' => ContactEngagementTimeline::route('/{record}/timeline'),
            'service-management' => ContactServiceManagement::route('/{record}/service-management'),
            'asset-management' => AssetManagement::route('/{record}/asset-management'),
        ];
    }
}
