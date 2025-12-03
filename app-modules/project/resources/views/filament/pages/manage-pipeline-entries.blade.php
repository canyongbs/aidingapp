{{--
    <COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Notice:

    - This software is closed source and the source code is a trade secret.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ is a registered trademarks of Canyon GBS LLC, and we are
      committed to enforcing and protecting our trademarks vigorously.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
--}}
<x-filament-panels::page @class([
    'fi-resource-manage-related-records-page',
    'fi-resource-' . str_replace('/', '-', $this->getResource()::getSlug()),
])>
    <div class="flex w-full justify-start">

        <div
            class="grid max-w-xs grid-cols-2 gap-1 rounded-lg bg-gray-100 p-1 dark:bg-gray-800"
            role="group"
        >
            <button
                type="button"
                @class([
                    'px-5 py-1.5 text-xs font-medium rounded-lg',
                    'text-white bg-gray-900 dark:bg-gray-300 dark:text-gray-900' =>
                        $viewType === 'table',
                    'text-gray-900 hover:bg-gray-200 dark:text-white dark:hover:bg-gray-700' =>
                        $viewType !== 'table',
                ])
                wire:click="setViewType('table')"
            >
                <x-filament::icon
                    class="h-6 w-6"
                    icon="heroicon-m-table-cells"
                />
            </button>
            <button
                type="button"
                @class([
                    'px-5 py-1.5 text-xs font-medium rounded-lg',
                    'text-white bg-gray-900 dark:bg-gray-300 dark:text-gray-900' =>
                        $viewType === 'kanban',
                    'text-gray-900 hover:bg-gray-200 dark:text-white dark:hover:bg-gray-700' =>
                        $viewType !== 'kanban',
                ])
                wire:click="setViewType('kanban')"
            >
                <x-filament::icon
                    class="h-6 w-6"
                    icon="heroicon-m-view-columns"
                />
            </button>
        </div>
    </div>

    @if ($viewType === 'table')

        @if ($this->table->getColumns())
            <div class="flex flex-col gap-y-6">
                @if (count($tabs = $this->getCachedTabs()))
                    @php
                        $activeTab = strval($this->activeTab);
                        $renderHookScopes = $this->getRenderHookScopes();
                    @endphp

                    <x-filament::tabs>
                        @foreach ($tabs as $tabKey => $tab)
                            @php
                                $tabKey = strval($tabKey);
                            @endphp

                            <x-filament::tabs.item
                                :active="$activeTab === $tabKey"
                                :badge="$tab->getBadge()"
                                :badge-color="$tab->getBadgeColor()"
                                :badge-icon="$tab->getBadgeIcon()"
                                :badge-icon-position="$tab->getBadgeIconPosition()"
                                :icon="$tab->getIcon()"
                                :icon-position="$tab->getIconPosition()"
                                :wire:click="'$set(\'activeTab\', ' . (filled($tabKey) ? ('\'' . $tabKey . '\'') : 'null') . ')'"
                                :attributes="$tab->getExtraAttributeBag()"
                            >
                                {{ $tab->getLabel() ?? $this->generateTabLabel($tabKey) }}
                            </x-filament::tabs.item>
                        @endforeach
                    </x-filament::tabs>
                @endif

                {{ $this->table }}
            </div>
        @endif
    @elseif($viewType === 'kanban')
        @livewire('pipeline-entries-kanban', [
            'pipeline' => $this->getOwnerRecord(),
        ])
        <x-filament-actions::modals />
    @endif

    @vite('app-modules/project/resources/js/kanban.js')

</x-filament-panels::page>
