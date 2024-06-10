<?php

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
                            ->label('Organization Name')
                            ->translateLabel(),
                        TextEntry::make('email')
                            ->label('Organization Email')
                            ->translateLabel(),
                        TextEntry::make('phone_number')
                            ->label('Organization Phone Number')
                            ->translateLabel(),
                        SpatieMediaLibraryImageEntry::make('logo')
                            ->visibility('private')
                            ->label('Organization Logo')
                            ->collection('organization_logo'),
                    ])
                    ->columns(),
                Section::make('Additional Info')
                    ->schema([
                        TextEntry::make('website')
                            ->label('Website')
                            ->translateLabel(),
                        TextEntry::make('industry.name')
                            ->label('Industry')
                            ->translateLabel(),
                        TextEntry::make('type.name')
                            ->label('Type')
                            ->translateLabel(),
                        TextEntry::make('description')
                            ->label('Description')
                            ->translateLabel(),
                        TextEntry::make('number_of_employees')
                            ->label('Number Of Employees')
                            ->translateLabel(),
                    ])
                    ->columns(),
                Section::make('Address Info')
                    ->schema([
                        TextEntry::make('address')
                            ->label('Address')
                            ->translateLabel(),
                        TextEntry::make('city')
                            ->label('City')
                            ->translateLabel(),
                        TextEntry::make('state')
                            ->label('State')
                            ->translateLabel(),
                        TextEntry::make('postalcode')
                            ->label('Postal Code')
                            ->translateLabel(),
                        TextEntry::make('country')
                            ->label('Country')
                            ->translateLabel(),
                    ])
                    ->columns(),
                Section::make('Social Media Info')
                    ->schema([
                        TextEntry::make('linkedin_url')
                            ->label('LinkedIn URL')
                            ->translateLabel(),
                        TextEntry::make('facebook_url')
                            ->label('Facebook URL')
                            ->translateLabel(),
                        TextEntry::make('twitter_url')
                            ->label('Twitter URL')
                            ->translateLabel(),
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
