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

namespace AidingApp\Contact\Filament\Resources\ContactResource\Pages;

use AidingApp\Contact\Filament\Resources\ContactResource;
use AidingApp\Contact\Models\Contact;
use AidingApp\Contact\Models\ContactType;
use AidingApp\Contact\Models\Organization;
use App\Concerns\EditPageRedirection;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class EditContact extends EditRecord
{
    use EditPageRedirection;

    protected static string $resource = ContactResource::class;

    // TODO: Automatically set from Filament
    protected static ?string $navigationLabel = 'Edit';

    public function form(Schema $schema): Schema
    {
        $generateFullName = function (Get $get, Set $set) {
            $title = $get('title') ?? '';

            $firstName = trim($get('first_name'));

            if (blank($firstName)) {
                return;
            }

            $lastName = trim($get('last_name'));

            if (blank($lastName)) {
                return;
            }

            $set(Contact::displayNameKey(), "{$title} {$firstName} {$lastName}");
        };

        return $schema
            ->components([
                Section::make('Demographics')
                    ->schema([
                        Select::make('title')
                            ->label('Title')
                            ->options([
                                'Dr.' => 'Dr.',
                                'Professor' => 'Professor',
                                'Mr.' => 'Mr.',
                                'Ms.' => 'Ms.',
                                'Mrs.' => 'Mrs.',
                            ])
                            ->in(['Dr.', 'Professor', 'Mr.', 'Ms.', 'Mrs.'])
                            ->nullable()
                            ->placeholder('No Title')
                            ->live(onBlur: true)
                            ->afterStateUpdated($generateFullName)
                            ->string()
                            ->searchable(),
                        TextInput::make('first_name')
                            ->label('First Name')
                            ->required()
                            ->string()
                            ->live(onBlur: true)
                            ->afterStateUpdated($generateFullName)
                            ->maxLength(255),
                        TextInput::make('last_name')
                            ->label('Last Name')
                            ->required()
                            ->string()
                            ->live(onBlur: true)
                            ->afterStateUpdated($generateFullName)
                            ->maxLength(255),
                        TextInput::make(Contact::displayNameKey())
                            ->label('Full Name')
                            ->required()
                            ->string()
                            ->disabled()
                            ->dehydrated()
                            ->maxLength(255),
                        TextInput::make('preferred')
                            ->label('Preferred Name')
                            ->string()
                            ->maxLength(255),
                        TextInput::make('job_title')
                            ->maxLength(255)
                            ->nullable()
                            ->string(),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),
                Section::make('Contact Information')
                    ->schema([
                        TextInput::make('email')
                            ->label('Primary Email')
                            ->email()
                            ->maxLength(255),
                        PhoneInput::make('mobile')
                            ->label('Mobile')
                            ->string(),
                        PhoneInput::make('phone')
                            ->label('Other Phone')
                            ->string(),
                        TextInput::make('address')
                            ->label('Address')
                            ->string()
                            ->maxLength(255),
                        TextInput::make('address_2')
                            ->label('Address 2')
                            ->string()
                            ->maxLength(255),
                        TextInput::make('address_3')
                            ->label('Address 3')
                            ->string()
                            ->maxLength(255),
                        TextInput::make('city')
                            ->label('City')
                            ->string()
                            ->maxLength(255),
                        TextInput::make('state')
                            ->label('State')
                            ->string()
                            ->maxLength(255),
                        TextInput::make('postal')
                            ->label('Postal')
                            ->string()
                            ->maxLength(255),
                    ])
                    ->columns(2),
                Section::make('Classification')
                    ->schema([
                        Select::make('type_id')
                            ->label('Type')
                            ->required()
                            ->relationship('type', 'name')
                            ->exists(
                                table: (new ContactType())->getTable(),
                                column: (new ContactType())->getKeyName()
                            ),
                        Select::make('organization_id')
                            ->label('Organization')
                            ->relationship('organization', 'name')
                            ->exists(
                                table: (new Organization())->getTable(),
                                column: (new Organization())->getKeyName()
                            ),
                        Textarea::make('description')
                            ->label('Description')
                            ->string()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Engagement Restrictions')
                    ->schema([
                        Radio::make('sms_opt_out')
                            ->label('SMS Opt Out')
                            ->boolean(),
                        Radio::make('email_bounce')
                            ->label('Email Bounce')
                            ->boolean(),
                    ])
                    ->columns(2),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
