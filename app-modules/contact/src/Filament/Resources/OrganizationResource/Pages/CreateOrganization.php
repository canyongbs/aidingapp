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

use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use AidingApp\Contact\Models\OrganizationType;
use AidingApp\Contact\Models\OrganizationIndustry;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use AidingApp\Contact\Filament\Resources\OrganizationResource;

class CreateOrganization extends CreateRecord
{
    protected static string $resource = OrganizationResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Primary Info')
                    ->columns()
                    ->schema([
                        TextInput::make('name')
                            ->label('Organization Name')
                            ->autofocus()
                            ->translateLabel()
                            ->maxLength(255)
                            ->required()
                            ->string()
                            ->placeholder('Organization Name'),
                        TextInput::make('email')
                            ->label('Organization Email')
                            ->translateLabel()
                            ->maxLength(255)
                            ->email()
                            ->placeholder('Organization Email'),
                        TextInput::make('phone_number')
                            ->label('Organization Phone Number')
                            ->translateLabel()
                            ->maxLength(255)
                            ->string()
                            ->placeholder('Organization Phone Number'),
                        SpatieMediaLibraryFileUpload::make('logo')
                            ->label('Organization Logo')
                            ->disk('s3')
                            ->collection('organization_logo')
                            ->image(),
                    ]),
                Section::make('Additional Info')
                    ->columns()
                    ->schema([
                        TextInput::make('website')
                            ->label('Website')
                            ->translateLabel()
                            ->maxLength(255)
                            ->activeUrl()
                            ->placeholder('Website'),
                        Select::make('industry_id')
                            ->label('Industry')
                            ->relationship('industry', 'name')
                            ->default(fn () => OrganizationIndustry::query()
                                ->where('is_default', true)
                                ->first() ? OrganizationIndustry::query()
                                ->where('is_default', true)
                                ->first()
                                ?->getKey() : ''),
                        Select::make('type_id')
                            ->label('Type')
                            ->relationship('type', 'name')
                            ->default(fn () => OrganizationType::query()
                                ->where('is_default', true)
                                ->first() ? OrganizationType::query()
                                ->where('is_default', true)
                                ->first()
                                ?->getKey() : ''),
                        Textarea::make('description')
                            ->label('Description')
                            ->string(),
                        TextInput::make('number_of_employees')
                            ->label('Number of Employees')
                            ->translateLabel()
                            ->numeric()
                            ->placeholder('Number Of Employees'),
                    ]),
                Section::make('Address Info')
                    ->columns()
                    ->schema([
                        TextInput::make('address')
                            ->label('Address')
                            ->translateLabel()
                            ->string()
                            ->placeholder('Address'),
                        TextInput::make('city')
                            ->label('City')
                            ->translateLabel()
                            ->maxLength(255)
                            ->string()
                            ->placeholder('City'),
                        TextInput::make('state')
                            ->label('State')
                            ->translateLabel()
                            ->maxLength(255)
                            ->string()
                            ->placeholder('State'),
                        TextInput::make('postalcode')
                            ->label('Postal Code')
                            ->translateLabel()
                            ->maxLength(255)
                            ->string()
                            ->placeholder('Postal Code'),
                        TextInput::make('country')
                            ->label('Country')
                            ->translateLabel()
                            ->maxLength(255)
                            ->string()
                            ->placeholder('Country'),
                    ]),
                Section::make('Social Media Info')
                    ->columns()
                    ->schema([
                        TextInput::make('linkedin_url')
                            ->label('LinkedIn URL')
                            ->translateLabel()
                            ->maxLength(255)
                            ->activeUrl()
                            ->placeholder('LinkedIn URL'),
                        TextInput::make('facebook_url')
                            ->label('Facebook URL')
                            ->translateLabel()
                            ->maxLength(255)
                            ->activeUrl()
                            ->placeholder('Facebook URL'),
                        TextInput::make('twitter_url')
                            ->label('Twitter URL')
                            ->translateLabel()
                            ->maxLength(255)
                            ->activeUrl()
                            ->placeholder('Twitter URL'),
                    ]),
            ]);
    }
}
