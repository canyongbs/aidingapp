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

namespace App\Filament\Resources\Users\Pages;

use AidingApp\Contact\Models\ContactType;
use App\Enums\PresenceStatus;
use App\Features\FullNameFeature;
use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use App\Rules\EmailNotInUseOrSoftDeleted;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use STS\FilamentImpersonate\Actions\Impersonate;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->disabled(false)
            ->components([
                Section::make('Demographic Information')
                    ->schema([
                        TextInput::make('first_name')
                            ->required()
                            ->string()
                            ->maxLength(255)
                            ->visible(fn (): bool => FullNameFeature::active()),
                        TextInput::make('last_name')
                            ->required()
                            ->string()
                            ->maxLength(255)
                            ->visible(fn (): bool => FullNameFeature::active()),
                        TextEntry::make('presence_status')
                            ->label('Presence')
                            ->state(fn (User $record): PresenceStatus => $record->presenceStatus())
                            ->badge()
                            ->color(fn (User $record) => $record->presenceStatus()->getColor())
                            ->icon(fn (User $record) => $record->presenceStatus()->getIcon())
                            ->formatStateUsing(fn (User $record) => $record->presenceStatus()->getLabel()),
                        TextInput::make('name')
                            ->label('Full Name')
                            ->maxLength(255)
                            ->disabled()
                            ->dehydrated(),
                        TextInput::make('preferred_name')
                            ->string()
                            ->maxLength(255)
                            ->visible(fn (): bool => FullNameFeature::active()),
                    ])
                    ->columns(2)
                    ->disabled(),
                Section::make('Employment Information')
                    ->schema([
                        TextInput::make('employee_id')
                            ->string()
                            ->maxLength(255)
                            ->visible(fn (): bool => FullNameFeature::active()),
                        TextInput::make('job_title')
                            ->string()
                            ->maxLength(255),
                        PhoneInput::make('work_number')
                            ->nullable()
                            ->label('Work Number'),
                        TextInput::make('work_extension')
                            ->label('Work Extension')
                            ->nullable()
                            ->numeric(),
                    ])
                    ->columns(2)
                    ->disabled(),
                Section::make('Academic Information')
                    ->schema([
                        TextInput::make('student_id')
                            ->string()
                            ->maxLength(255),
                        TextInput::make('school')
                            ->string()
                            ->maxLength(255),
                        TextInput::make('academic_department')
                            ->string()
                            ->maxLength(255),
                        TextInput::make('program')
                            ->string()
                            ->maxLength(255),
                    ])
                    ->visible(fn (): bool => FullNameFeature::active())
                    ->columns(2)
                    ->disabled(),
                Section::make('Contact Information')
                    ->schema([
                        TextInput::make('email')
                            ->label('Email address')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->rules([
                                new EmailNotInUseOrSoftDeleted($this->getRecord()->getKey()),
                            ]),
                        PhoneInput::make('mobile')
                            ->nullable(),
                    ])
                    ->columns(2)
                    ->disabled(),
                Section::make('Address Information')
                    ->schema([
                        TextInput::make('address')
                            ->label('Address')
                            ->string()
                            ->maxLength(255),
                        TextInput::make('address_2')
                            ->label('Address 2')
                            ->string()
                            ->maxLength(255),
                        TextInput::make('city')
                            ->string()
                            ->maxLength(255),
                        TextInput::make('state')
                            ->string()
                            ->maxLength(255),
                        TextInput::make('postal_code')
                            ->label('Postal')
                            ->string()
                            ->maxLength(255),
                        TextInput::make('country')
                            ->string()
                            ->maxLength(255),
                    ])
                    ->visible(fn (): bool => FullNameFeature::active())
                    ->columns(2)
                    ->disabled(),
                Section::make('Account Settings')
                    ->schema([
                        Toggle::make('is_managed_contact')
                            ->label('Managed Contact')
                            ->helperText('Creates a linked, read-only contact record for the self-service portal that stays in sync with this user.')
                            ->live(),
                        Select::make('managed_contact_type_id')
                            ->label('Type')
                            ->options(fn (): array => ContactType::query()->pluck('name', 'id')->all())
                            ->searchable()
                            ->preload()
                            ->required(fn (Get $get): bool => (bool) $get('is_managed_contact'))
                            ->visible(fn (Get $get): bool => (bool) $get('is_managed_contact')),
                        Toggle::make('is_external')
                            ->label('User can only log in via a social provider.'),
                    ])
                    ->disabled(),
                Section::make('System Information')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->label('Updated At')
                            ->dateTime(),
                    ])
                    ->columns(2),
                Section::make('Department')
                    ->schema([
                        Select::make('department_id')
                            ->hiddenLabel()
                            ->relationship('department', 'name')
                            ->disabled(),
                    ])
                    ->hidden(fn (?User $record) => $record?->isAdmin() ?? false),
            ]);
    }

    /**
     * @param  array<string, mixed>  $data
     *
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $user = $this->getRecord();

        assert($user instanceof User);

        $managedContact = $user->managedContact()->first();

        $data['is_managed_contact'] = ! is_null($managedContact);
        $data['managed_contact_type_id'] = $managedContact?->type_id;

        return $data;
    }

    protected function getHeaderActions(): array
    {
        /** @var User $user */
        $user = $this->getRecord();

        return [
            Impersonate::make()
                ->record($user),
            EditAction::make(),
        ];
    }
}
