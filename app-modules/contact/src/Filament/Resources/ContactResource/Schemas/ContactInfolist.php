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

use AidingApp\Contact\Filament\Resources\ContactResource\RelationManagers\AssetCheckInRelationManager;
use AidingApp\Contact\Filament\Resources\ContactResource\RelationManagers\AssetCheckOutRelationManager;
use AidingApp\Contact\Filament\Resources\ContactResource\RelationManagers\EngagementsRelationManager;
use AidingApp\Contact\Filament\Resources\ContactResource\RelationManagers\ServiceRequestsRelationManager;
use AidingApp\Contact\Models\Contact;
use AidingApp\Engagement\Filament\Resources\EngagementFiles\RelationManagers\EngagementFilesRelationManager;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Tabs\Tab;

class ContactInfolist
{
    /**
     * @return array<Tab>
     */
    public static function tabs(Contact $contact, string $pageClass): array
    {
        // Relation managers enforce their own authorization on mount, and Tabs are not
        // lazy-loaded, so an inaccessible manager must be omitted here entirely rather
        // than merely hidden, or it will 403 the whole page instead of just its tab.
        $tabs = [];

        if (ServiceRequestsRelationManager::canViewForRecord($contact, $pageClass)) {
            $tabs[] = Tab::make('Service Requests')
                ->schema([
                    Livewire::make(ServiceRequestsRelationManager::class, [
                        'ownerRecord' => $contact,
                        'pageClass' => $pageClass,
                    ])->key(ServiceRequestsRelationManager::class),
                ]);
        }

        $assetSections = array_filter([
            AssetCheckOutRelationManager::canViewForRecord($contact, $pageClass)
                ? Livewire::make(AssetCheckOutRelationManager::class, [
                    'ownerRecord' => $contact,
                    'pageClass' => $pageClass,
                ])->key(AssetCheckOutRelationManager::class)
                : null,
            AssetCheckInRelationManager::canViewForRecord($contact, $pageClass)
                ? Livewire::make(AssetCheckInRelationManager::class, [
                    'ownerRecord' => $contact,
                    'pageClass' => $pageClass,
                ])->key(AssetCheckInRelationManager::class)
                : null,
        ]);

        if (filled($assetSections)) {
            $tabs[] = Tab::make('Assets')->schema($assetSections);
        }

        if (EngagementFilesRelationManager::canViewForRecord($contact, $pageClass)) {
            $tabs[] = Tab::make('Files')
                ->schema([
                    Livewire::make(EngagementFilesRelationManager::class, [
                        'ownerRecord' => $contact,
                        'pageClass' => $pageClass,
                    ])->key(EngagementFilesRelationManager::class),
                ]);
        }

        if (EngagementsRelationManager::canViewForRecord($contact, $pageClass)) {
            $tabs[] = Tab::make('Emails')
                ->schema([
                    Livewire::make(EngagementsRelationManager::class, [
                        'ownerRecord' => $contact,
                        'pageClass' => $pageClass,
                    ])->key(EngagementsRelationManager::class),
                ]);
        }

        return $tabs;
    }
}
