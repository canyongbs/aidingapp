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

namespace AidingApp\Contact\Filament\Resources\ContactResource\Actions;

use AidingApp\Contact\Filament\Resources\ContactResource;
use AidingApp\Contact\Filament\Resources\ContactResource\Pages\ViewContact;
use AidingApp\Contact\Filament\Resources\ContactResource\RelationManagers\AssetCheckInRelationManager;
use AidingApp\Contact\Filament\Resources\ContactResource\RelationManagers\AssetCheckOutRelationManager;
use AidingApp\Contact\Filament\Resources\ContactResource\RelationManagers\EngagementsRelationManager;
use AidingApp\Contact\Filament\Resources\ContactResource\RelationManagers\ServiceRequestsRelationManager;
use AidingApp\Contact\Filament\Resources\ContactResource\Schemas\ContactInfolist;
use AidingApp\Contact\Models\Contact;
use AidingApp\Engagement\Filament\Resources\EngagementFiles\RelationManagers\EngagementFilesRelationManager;
use Filament\Actions\Action;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;

class ViewContactAction
{
    public static function make(Contact $contact): Action
    {
        return Action::make('viewContact')
            ->label('View contact')
            ->icon(Heroicon::Eye)
            ->iconButton()
            ->record($contact)
            ->authorize('view', $contact)
            ->slideOver()
            ->modalWidth(Width::FourExtraLarge)
            ->modalHeading($contact->{Contact::displayNameKey()})
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Close')
            ->schema([
                ...ContactInfolist::sections(),
                Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Service Requests')
                            ->visible(fn (): bool => ServiceRequestsRelationManager::canViewForRecord($contact, ViewContact::class))
                            ->schema([
                                Livewire::make(ServiceRequestsRelationManager::class, [
                                    'ownerRecord' => $contact,
                                    'pageClass' => ViewContact::class,
                                ])
                                    ->key(ServiceRequestsRelationManager::class)
                                    ->lazy(),
                            ]),
                        Tab::make('Assets')
                            ->visible(
                                fn (): bool => AssetCheckOutRelationManager::canViewForRecord($contact, ViewContact::class)
                                    || AssetCheckInRelationManager::canViewForRecord($contact, ViewContact::class)
                            )
                            ->schema([
                                Section::make('Checked Out Assets')
                                    ->visible(fn (): bool => AssetCheckOutRelationManager::canViewForRecord($contact, ViewContact::class))
                                    ->schema([
                                        Livewire::make(AssetCheckOutRelationManager::class, [
                                            'ownerRecord' => $contact,
                                            'pageClass' => ViewContact::class,
                                        ])
                                            ->key(AssetCheckOutRelationManager::class)
                                            ->lazy(),
                                    ]),
                                Section::make('Returned Assets')
                                    ->visible(fn (): bool => AssetCheckInRelationManager::canViewForRecord($contact, ViewContact::class))
                                    ->schema([
                                        Livewire::make(AssetCheckInRelationManager::class, [
                                            'ownerRecord' => $contact,
                                            'pageClass' => ViewContact::class,
                                        ])
                                            ->key(AssetCheckInRelationManager::class)
                                            ->lazy(),
                                    ]),
                            ]),
                        Tab::make('Files')
                            ->visible(fn (): bool => EngagementFilesRelationManager::canViewForRecord($contact, ViewContact::class))
                            ->schema([
                                Livewire::make(EngagementFilesRelationManager::class, [
                                    'ownerRecord' => $contact,
                                    'pageClass' => ViewContact::class,
                                ])
                                    ->key(EngagementFilesRelationManager::class)
                                    ->lazy(),
                            ]),
                        Tab::make('Emails')
                            ->visible(fn (): bool => EngagementsRelationManager::canViewForRecord($contact, ViewContact::class))
                            ->schema([
                                Livewire::make(EngagementsRelationManager::class, [
                                    'ownerRecord' => $contact,
                                    'pageClass' => ViewContact::class,
                                ])
                                    ->key(EngagementsRelationManager::class)
                                    ->lazy(),
                            ]),
                    ]),
            ])
            ->extraModalFooterActions([
                Action::make('goToContact')
                    ->label('Go to Contact')
                    ->url(ContactResource::getUrl('view', ['record' => $contact])),
            ]);
    }
}
