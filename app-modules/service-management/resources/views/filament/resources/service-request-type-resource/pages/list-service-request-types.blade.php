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

<x-filament-panels::page
    @class(['fi-resource-list-records-page', 'fi-resource-' . str_replace('/', '-', $this->getResource()::getSlug())])
>
    <div
        class="flex flex-col gap-6"
        id="service-request-type-manager"
        wire:ignore
        x-data="serviceRequestTypeManager({
                    originalTreeData: @js($this->hierarchicalData),
                    treeData: @js($this->hierarchicalData),
                    canEdit: @js($this->canEdit),
                })"
    >
        {{-- Sticky Save Banner --}}
        <div
            class="sticky top-20 z-50"
            style="display: none"
            x-show="hasUnsavedChanges && canEdit"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
        >
            <x-filament::callout
                color="warning"
                icon="heroicon-m-exclamation-triangle"
                heading="You have unsaved changes"
                description="Save your changes or they will be lost"
            >
                <x-slot name="footer">
                    <div class="flex gap-3">
                        <x-filament::button
                            @click="saveChanges()"
                            x-bind:disabled="isSaving"
                            icon="heroicon-m-arrow-down-tray"
                        >
                            <span x-text="isSaving ? 'Saving...' : 'Save Changes'"></span>
                        </x-filament::button>

                        <x-filament::button
                            @click="discardChanges()"
                            x-bind:disabled="isSaving"
                            color="gray"
                            icon="heroicon-m-x-mark"
                        >
                            Discard
                        </x-filament::button>
                    </div>
                </x-slot>
            </x-filament::callout>
        </div>

        {{-- Top Action Buttons --}}
        <div class="flex flex-wrap items-center justify-between gap-4" x-show="canEdit">
            {{-- Expand/Collapse Links --}}
            <div class="flex gap-3" x-show="treeData.categories && treeData.categories.length > 0">
                <x-filament::link tag="button" type="button" @click="expandAll()" size="sm">
                    Expand All
                </x-filament::link>
                <x-filament::link tag="button" type="button" @click="collapseAll()" size="sm">
                    Collapse All
                </x-filament::link>
            </div>

            {{-- Add Service Request Area/Type Buttons (Right) --}}
            <div class="flex w-full justify-end gap-3 sm:w-auto">
                {{-- Add Service Request Area Button --}}
                <div id="show-category-wrapper">
                    <x-filament::button id="show-category-btn" type="button" color="gray" outlined="true" size="sm">
                        Add Service Request Area
                    </x-filament::button>
                    <div
                        class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row"
                        id="category-input-form"
                        style="display: none"
                    >
                        <div class="w-full">
                            <input
                                class="focus:border-primary-500 focus:ring-primary-500 block h-8 w-full rounded-lg border-gray-300 shadow-sm sm:text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                id="new-category-name"
                                type="text"
                                placeholder="Name of new service request area"
                            />
                        </div>
                        <div class="flex gap-2">
                            <x-filament::button id="create-category-btn" type="button" size="sm">
                                Add
                            </x-filament::button>
                            <x-filament::button id="cancel-category-btn" type="button" color="gray" size="sm">
                                Cancel
                            </x-filament::button>
                        </div>
                    </div>
                </div>

                {{-- Add Type Button --}}
                <div id="show-type-wrapper">
                    <x-filament::button id="show-type-btn" type="button" color="gray" outlined="true" size="sm">
                        Add Type
                    </x-filament::button>
                    <div
                        class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row"
                        id="type-input-form"
                        style="display: none"
                    >
                        <div class="w-full">
                            <input
                                class="focus:border-primary-500 focus:ring-primary-500 block h-8 w-full rounded-lg border-gray-300 shadow-sm sm:text-sm dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                id="new-type-name"
                                type="text"
                                placeholder="Name of new type"
                                x-bind:disabled="isCheckingType"
                            />
                        </div>
                        <div class="flex gap-2">
                            <x-filament::button
                                id="create-type-btn"
                                type="button"
                                size="sm"
                                x-bind:disabled="isCheckingType"
                            >
                                <span x-show="!isCheckingType">Add</span>
                                <x-filament::loading-indicator x-show="isCheckingType" class="h-4 w-4" />
                            </x-filament::button>
                            <x-filament::button id="cancel-type-btn" type="button" color="gray" size="sm">
                                Cancel
                            </x-filament::button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Hierarchical Tree --}}
        <div>
            <div class="space-y-2">
                {{-- Uncategorized Types (at top) --}}
                <div
                    class="mb-4 min-h-4 space-y-1 rounded p-0.5 transition-colors duration-150 ease-in-out"
                    id="uncategorized-types"
                    data-sortable="types"
                    data-category-id=""
                ></div>

                {{-- Root Level Categories --}}
                <div
                    class="flex min-h-5 flex-col gap-2 rounded-md p-0.5 transition-colors duration-150 ease-in-out"
                    id="root-categories"
                    data-sortable="categories"
                    data-parent-id=""
                ></div>

                {{-- Empty state: show when there are no categories and no types --}}
                <div
                    class="flex flex-col items-center justify-center gap-3 py-12 text-center"
                    style="min-height: 120px"
                    x-cloak
                    x-show="
                        (! treeData.categories || treeData.categories.length === 0) &&
                            (! treeData.uncategorized_types || treeData.uncategorized_types.length === 0)
                    "
                >
                    {{-- Icon rendered server-side via @svg helper, pass heroicon name --}}
                    <div class="flex items-center justify-center">
                        @svg('heroicon-m-inbox', 'class="h-12 w-12 text-gray-400 dark:text-gray-400"')
                    </div>

                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        No types or service request areas yet
                    </h3>
                    <p class="max-w-xl text-sm text-gray-600 dark:text-gray-400">
                        Create a service request area or add a type to get started. Use the buttons below to add your
                        first items.
                    </p>
                </div>
            </div>
        </div>

        {{-- Bottom Action Buttons --}}
        <div class="flex flex-col items-center justify-between gap-4 sm:flex-row sm:items-center" x-show="canEdit">
            {{-- Save/Discard Buttons --}}
            <div class="flex w-full justify-start gap-3 sm:w-auto">
                <x-filament::button
                    id="save-changes-btn"
                    type="button"
                    size="sm"
                    icon="heroicon-m-check"
                    x-bind:disabled="isSaving"
                    x-show="hasUnsavedChanges"
                    @click="saveChanges()"
                >
                    <span x-text="isSaving ? 'Saving...' : 'Save Changes'"></span>
                </x-filament::button>

                <x-filament::button
                    id="discard-changes-btn"
                    type="button"
                    color="gray"
                    size="sm"
                    icon="heroicon-m-x-mark"
                    x-bind:disabled="isSaving"
                    x-show="hasUnsavedChanges"
                    @click="discardChanges()"
                >
                    Discard Changes
                </x-filament::button>
            </div>
        </div>

        {{-- Archived Type Restore Modal --}}
        <x-filament::modal id="archived-type-restore-modal" width="md">
            <x-slot name="heading">Service Request Type Already Exists</x-slot>

            <p class="text-sm text-gray-600 dark:text-gray-400">
                A service request type named
                <span class="font-semibold" x-text="'“' + pendingRestore?.archivedType?.name + '”'"></span>
                already exists and is currently archived.
            </p>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                To use this name, you can restore the archived type. Or, select Cancel to create a new type with a
                different name.
            </p>

            <x-slot name="footer">
                <x-filament::actions full-width>
                    <x-filament::button
                        color="gray"
                        @click="$dispatch('close-modal', { id: 'archived-type-restore-modal' })"
                    >
                        Cancel
                    </x-filament::button>

                    <x-filament::button
                        @click="confirmRestore(); $dispatch('close-modal', { id: 'archived-type-restore-modal' })"
                    >
                        Restore
                    </x-filament::button>
                </x-filament::actions>
            </x-slot>
        </x-filament::modal>
    </div>

    @assets
        @vite(['app-modules/service-management/resources/js/serviceRequestTypeManager.js'])

        <style>
            .drop-line {
                position: absolute;
                left: 0;
                right: 0;
                height: 2px;
                background: rgb(248, 162, 8);
                border-radius: 1px;
                box-shadow: 0 0 6px rgba(248, 162, 8, 0.5);
                z-index: 1000;
                pointer-events: none;
            }

            .drop-line::before,
            .drop-line::after {
                content: '';
                position: absolute;
                top: -3px;
                width: 8px;
                height: 8px;
                background: rgb(248, 162, 8);
                border-radius: 50%;
            }

            .drop-line::before {
                left: -6px;
            }

            .drop-line::after {
                right: -6px;
            }

            .nest-target::after {
                content: 'Drop to add as child';
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                background: rgb(248, 162, 8);
                color: white;
                padding: 4px 8px;
                border-radius: 4px;
                font-size: 12px;
                font-weight: 500;
                pointer-events: none;
                z-index: 10;
            }

            .updating-order::after {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgb(255 255 255 / 0.8);
                border-radius: 8px;
                z-index: 10;
            }

            [data-sortable='categories'],
            [data-sortable='types'] {
                position: relative;
            }
        </style>
    @endassets
</x-filament-panels::page>
