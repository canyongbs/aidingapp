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

namespace AidingApp\Contact\Filament\Resources\ContactResource\Schemas;

use AidingApp\Contact\Models\Contact;
use AidingApp\Contact\Models\ContactType;
use AidingApp\Contact\Models\Organization;
use App\Features\EnhanceContactsTableDataModelFeature;
use App\Filament\Forms\Components\AddressInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Validation\Rules\Unique;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class ContactFormSchema
{
    /**
     * @return array<int, Section>
     */
    public static function make(bool $ignoreRecord = false): array
    {
        return [
            self::demographicInformation(),
            self::contactInformation($ignoreRecord),
            self::employmentInformation(),
            // TODO: Cleanup Task (enhance-contacts-data-model): always include the academic section once the flag is removed.
            ...(EnhanceContactsTableDataModelFeature::active() ? [self::academicInformation()] : []),
            self::addressInformation(),
            self::customerInformation(),
        ];
    }

    protected static function demographicInformation(): Section
    {
        $generateFullName = function (Get $get, Set $set): void {
            $firstName = trim((string) $get('first_name'));

            if (blank($firstName)) {
                return;
            }

            $lastName = trim((string) $get('last_name'));

            if (blank($lastName)) {
                return;
            }

            $set(Contact::displayNameKey(), "{$firstName} {$lastName}");
        };

        return Section::make('Demographic Information')
            ->schema([
                TextInput::make('first_name')
                    ->label('First Name')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated($generateFullName)
                    ->string()
                    ->maxLength(255),
                TextInput::make('last_name')
                    ->label('Last Name')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated($generateFullName)
                    ->string()
                    ->maxLength(255),
                TextInput::make(Contact::displayNameKey())
                    ->label('Full Name')
                    ->required()
                    ->disabled()
                    ->dehydrated()
                    ->string()
                    ->maxLength(255),
                TextInput::make('preferred')
                    ->label('Preferred Name')
                    ->string()
                    ->maxLength(255),
                Select::make('type_id')
                    ->label('Type')
                    ->required()
                    ->relationship('type', 'name')
                    ->default(fn (): ?string => ContactType::resolveDefault()?->getKey())
                    ->exists(
                        table: (new ContactType())->getTable(),
                        column: (new ContactType())->getKeyName()
                    ),
            ])
            ->columns(2);
    }

    protected static function contactInformation(bool $ignoreRecord): Section
    {
        return Section::make('Contact Information')
            ->schema([
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->maxLength(255)
                    ->unique(ignoreRecord: $ignoreRecord, modifyRuleUsing: fn (Unique $rule) => $rule->withoutTrashed()),
                PhoneInput::make('mobile')
                    ->label('Mobile')
                    ->string(),
                PhoneInput::make('phone')
                    ->label('Phone')
                    ->string(),
            ])
            ->columns(2);
    }

    protected static function employmentInformation(): Section
    {
        return Section::make('Employment Information')
            ->schema([
                TextInput::make('job_title')
                    ->label('Job Title')
                    ->string()
                    ->maxLength(255),
                // TODO: Cleanup Task (enhance-contacts-data-model): unwrap these fields once the flag is removed.
                ...(EnhanceContactsTableDataModelFeature::active() ? [
                    TextInput::make('employee_id')
                        ->label('Employee ID')
                        ->string()
                        ->maxLength(255),
                    TextInput::make('work_number')
                        ->label('Work Number')
                        ->string()
                        ->maxLength(255),
                    TextInput::make('work_extension')
                        ->label('Work Extension')
                        ->string()
                        ->maxLength(255),
                ] : []),
            ])
            ->columns(2);
    }

    protected static function academicInformation(): Section
    {
        return Section::make('Academic Information')
            ->schema([
                TextInput::make('student_id')
                    ->label('Student ID')
                    ->string()
                    ->maxLength(255),
                TextInput::make('school')
                    ->label('School')
                    ->string()
                    ->maxLength(255),
                TextInput::make('academic_department')
                    ->label('Academic Department')
                    ->string()
                    ->maxLength(255),
                TextInput::make('program')
                    ->label('Program')
                    ->string()
                    ->maxLength(255),
            ])
            ->columns(2);
    }

    protected static function addressInformation(): Section
    {
        return Section::make('Address Information')
            ->schema([
                AddressInput::make([
                    'address' => 'address',
                    'city' => 'city',
                    'state' => 'state',
                    'postal' => 'postalCode',
                    // TODO: Cleanup Task (enhance-contacts-data-model): always map the country component once the flag is removed.
                    ...(EnhanceContactsTableDataModelFeature::active() ? ['country' => 'country'] : []),
                ]),
                TextInput::make('address_2')
                    ->label('Address 2')
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
                // TODO: Cleanup Task (enhance-contacts-data-model): unwrap this field once the flag is removed.
                ...(EnhanceContactsTableDataModelFeature::active() ? [
                    TextInput::make('country')
                        ->label('Country')
                        ->string()
                        ->maxLength(255),
                ] : []),
            ])
            ->columns(2);
    }

    protected static function customerInformation(): Section
    {
        return Section::make('Customer Information')
            ->schema([
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
            ->columns(2);
    }
}
