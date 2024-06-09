<?php

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
