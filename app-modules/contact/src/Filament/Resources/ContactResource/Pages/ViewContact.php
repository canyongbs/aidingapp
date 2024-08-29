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

namespace AidingApp\Contact\Filament\Resources\ContactResource\Pages;

use Filament\Actions\EditAction;
use Filament\Infolists\Infolist;
use AidingApp\Contact\Models\Contact;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use AidingApp\Contact\Filament\Resources\ContactResource;
use AidingApp\Notification\Filament\Actions\SubscribeHeaderAction;

class ViewContact extends ViewRecord
{
    protected static string $resource = ContactResource::class;

    // TODO: Automatically set from Filament
    protected static ?string $navigationLabel = 'View';

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Demographics')
                    ->schema([
                        TextEntry::make('first_name')
                            ->label('First Name'),
                        TextEntry::make('last_name')
                            ->label('Last Name'),
                        TextEntry::make(Contact::displayNameKey())
                            ->label('Full Name'),
                        TextEntry::make('preferred')
                            ->label('Preferred Name'),
                    ])
                    ->columns(2),
                Section::make('Contact Information')
                    ->schema([
                        TextEntry::make('email')
                            ->label('Email'),
                        TextEntry::make('mobile')
                            ->label('Mobile'),
                        TextEntry::make('phone')
                            ->label('Phone'),
                        TextEntry::make('address')
                            ->label('Address'),
                        TextEntry::make('address_2')
                            ->label('Address 2'),
                        TextEntry::make('address_3')
                            ->label('Address 3'),
                        TextEntry::make('city')
                            ->label('City'),
                        TextEntry::make('state')
                            ->label('State'),
                        TextEntry::make('postal')
                            ->label('Postal'),
                    ])
                    ->columns(2),
                Section::make('Classification')
                    ->schema([
                        TextEntry::make('status.name')
                            ->label('Status'),
                        TextEntry::make('source.name')
                            ->label('Source'),
                        TextEntry::make('description')
                            ->label('Description')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Engagement Restrictions')
                    ->schema([
                        IconEntry::make('sms_opt_out')
                            ->label('SMS Opt Out')
                            ->boolean(),
                        IconEntry::make('email_bounce')
                            ->label('Email Bounce')
                            ->boolean(),
                    ])
                    ->columns(2),
                Section::make('Record Details')
                    ->schema([
                        TextEntry::make('createdBy.name')
                            ->label('Created By'),
                        TextEntry::make('assignedTo.name')
                            ->label('Assigned To'),
                    ])
                    ->columns(2),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            SubscribeHeaderAction::make(),
        ];
    }
}
