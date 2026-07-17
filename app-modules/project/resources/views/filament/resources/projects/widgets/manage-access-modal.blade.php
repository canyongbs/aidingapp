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

<div x-data="{ tab: 'managers' }" class="max-h-96 space-y-6">
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
