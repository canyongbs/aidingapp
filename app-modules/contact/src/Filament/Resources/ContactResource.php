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

namespace AdvisingApp\Contact\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Resources\Pages\Page;
use AdvisingApp\Contact\Models\Contact;
use Illuminate\Database\Eloquent\Model;
use AdvisingApp\Contact\Filament\Resources\ContactResource\Pages\EditContact;
use AdvisingApp\Contact\Filament\Resources\ContactResource\Pages\ViewContact;
use AdvisingApp\Contact\Filament\Resources\ContactResource\Pages\ListContacts;
use AdvisingApp\Contact\Filament\Resources\ContactResource\Pages\CreateContact;
use AdvisingApp\Contact\Filament\Resources\ContactResource\Pages\ManageContactFiles;
use AdvisingApp\Contact\Filament\Resources\ContactResource\Pages\ManageContactTasks;
use AdvisingApp\Contact\Filament\Resources\ContactResource\Pages\ManageContactAlerts;
use AdvisingApp\Contact\Filament\Resources\ContactResource\Pages\ManageContactCareTeam;
use AdvisingApp\Contact\Filament\Resources\ContactResource\Pages\ManageContactEngagement;
use AdvisingApp\Contact\Filament\Resources\ContactResource\Pages\ContactServiceManagement;
use AdvisingApp\Contact\Filament\Resources\ContactResource\Pages\ContactEngagementTimeline;
use AdvisingApp\Contact\Filament\Resources\ContactResource\Pages\ManageContactInteractions;
use AdvisingApp\Contact\Filament\Resources\ContactResource\Pages\ManageContactSubscriptions;
use AdvisingApp\Contact\Filament\Resources\ContactResource\Pages\ManageContactFormSubmissions;

class ContactResource extends Resource
{
    protected static ?string $model = Contact::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Recruitment CRM';

    protected static ?int $navigationSort = 20;

    protected static ?string $recordTitleAttribute = 'full_name';

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewContact::class,
            EditContact::class,
            ManageContactEngagement::class,
            ManageContactFiles::class,
            ManageContactAlerts::class,
            ManageContactTasks::class,
            ManageContactSubscriptions::class,
            ManageContactInteractions::class,
            ContactEngagementTimeline::class,
            ManageContactCareTeam::class,
            ManageContactFormSubmissions::class,
            ContactServiceManagement::class,
        ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['full_name', 'email', 'email_2', 'mobile', 'phone'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return array_filter([
            'Student ID' => $record->sisid,
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
            'manage-form-submissions' => ManageContactFormSubmissions::route('/{record}/form-submissions'),
            'manage-interactions' => ManageContactInteractions::route('/{record}/interactions'),
            'manage-subscriptions' => ManageContactSubscriptions::route('/{record}/subscriptions'),
            'manage-tasks' => ManageContactTasks::route('/{record}/tasks'),
            'view' => ViewContact::route('/{record}'),
            'timeline' => ContactEngagementTimeline::route('/{record}/timeline'),
            'care-team' => ManageContactCareTeam::route('/{record}/care-team'),
            'service-management' => ContactServiceManagement::route('/{record}/service-management'),
        ];
    }
}
