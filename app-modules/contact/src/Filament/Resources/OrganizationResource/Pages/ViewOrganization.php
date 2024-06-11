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

namespace AidingApp\Contact\Filament\Resources\OrganizationResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use AidingApp\Contact\Filament\Resources\OrganizationResource;
use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;

class ViewOrganization extends ViewRecord
{
    protected static string $resource = OrganizationResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Primary Info')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Organization Name'),
                        TextEntry::make('email')
                            ->label('Organization Email'),
                        TextEntry::make('phone_number')
                            ->label('Organization Phone Number'),
                        SpatieMediaLibraryImageEntry::make('logo')
                            ->visibility('private')
                            ->label('Organization Logo')
                            ->collection('organization_logo'),
                    ])
                    ->columns(),
                Section::make('Additional Info')
                    ->schema([
                        TextEntry::make('website')
                            ->label('Website'),
                        TextEntry::make('industry.name')
                            ->label('Industry'),
                        TextEntry::make('type.name')
                            ->label('Type'),
                        TextEntry::make('description')
                            ->label('Description'),
                        TextEntry::make('number_of_employees')
                            ->label('Number Of Employees'),
                    ])
                    ->columns(),
                Section::make('Address Info')
                    ->schema([
                        TextEntry::make('address')
                            ->label('Address'),
                        TextEntry::make('city')
                            ->label('City'),
                        TextEntry::make('state')
                            ->label('State'),
                        TextEntry::make('postalcode')
                            ->label('Postal Code'),
                        TextEntry::make('country')
                            ->label('Country'),
                    ])
                    ->columns(),
                Section::make('Social Media Info')
                    ->schema([
                        TextEntry::make('linkedin_url')
                            ->label('LinkedIn URL'),
                        TextEntry::make('facebook_url')
                            ->label('Facebook URL'),
                        TextEntry::make('twitter_url')
                            ->label('Twitter URL'),
                    ])
                    ->columns(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
