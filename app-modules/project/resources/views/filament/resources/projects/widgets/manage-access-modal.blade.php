{{--
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
--}}
@php
    use AidingApp\Project\Filament\Resources\Projects\Pages\ManageManagers;
    use AidingApp\Project\Filament\Resources\Projects\Pages\ManageAuditors;
    use AidingApp\Project\Filament\Resources\Projects\Pages\ManageGuests;
    use AidingApp\Project\Filament\Resources\Projects\RelationManagers\ManagerUsersRelationManager;
    use AidingApp\Project\Filament\Resources\Projects\RelationManagers\ManagerDepartmentsRelationManager;
    use AidingApp\Project\Filament\Resources\Projects\RelationManagers\AuditorUsersRelationManager;
    use AidingApp\Project\Filament\Resources\Projects\RelationManagers\AuditorDepartmentsRelationManager;
    use AidingApp\Project\Filament\Resources\Projects\RelationManagers\GuestContactsRelationManager;
    use AidingApp\Project\Filament\Resources\Projects\RelationManagers\GuestOrganizationsRelationManager;
@endphp

<div x-data="{ tab: 'managers' }" class="space-y-6">
    <div class="flex justify-center">
        <x-filament::tabs class>
            <x-filament::tabs.item
                tag="button"
                x-on:click="tab = 'managers'"
                :alpine-active="'tab === \'managers\''"
                icon="heroicon-m-user-group"
            >
                Managers
            </x-filament::tabs.item>

            <x-filament::tabs.item
                tag="button"
                x-on:click="tab = 'auditors'"
                :alpine-active="'tab === \'auditors\''"
                icon="heroicon-m-shield-check"
            >
                Auditors
            </x-filament::tabs.item>

            <x-filament::tabs.item
                tag="button"
                x-on:click="tab = 'guests'"
                :alpine-active="'tab === \'guests\''"
                icon="heroicon-m-users"
            >
                Guests
            </x-filament::tabs.item>
        </x-filament::tabs>
    </div>

    {{-- Managers --}}
    <div x-show="tab === 'managers'" x-cloak class="space-y-6">
        @livewire(
            ManagerUsersRelationManager::class,
            ['ownerRecord' => $project, 'pageClass' => ManageManagers::class],
            key('project-access-manager-users-' . $project->getKey())
        )

        @livewire(
            ManagerDepartmentsRelationManager::class,
            ['ownerRecord' => $project, 'pageClass' => ManageManagers::class],
            key('project-access-manager-departments-' . $project->getKey())
        )
    </div>

    {{-- Auditors --}}
    <div x-show="tab === 'auditors'" x-cloak class="space-y-6">
        @livewire(
            AuditorUsersRelationManager::class,
            ['ownerRecord' => $project, 'pageClass' => ManageAuditors::class],
            key('project-access-auditor-users-' . $project->getKey())
        )

        @livewire(
            AuditorDepartmentsRelationManager::class,
            ['ownerRecord' => $project, 'pageClass' => ManageAuditors::class],
            key('project-access-auditor-departments-' . $project->getKey())
        )
    </div>

    {{-- Guests --}}
    <div x-show="tab === 'guests'" x-cloak class="space-y-6">
        @livewire(
            GuestContactsRelationManager::class,
            ['ownerRecord' => $project, 'pageClass' => ManageGuests::class],
            key('project-access-guest-contacts-' . $project->getKey())
        )

        @livewire(
            GuestOrganizationsRelationManager::class,
            ['ownerRecord' => $project, 'pageClass' => ManageGuests::class],
            key('project-access-guest-organizations-' . $project->getKey())
        )
    </div>
</div>
