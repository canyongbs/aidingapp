{{--
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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
--}}
@php
    use Filament\Support\Facades\FilamentAsset;
@endphp

<x-filament-panels::page @class([
    'fi-resource-list-records-page',
    'fi-resource-' . str_replace('/', '-', $this->getResource()::getSlug()),
])>
    <div
        class="space-y-6"
        id="service-request-type-manager"
        wire:ignore
        x-data="serviceRequestTypeManager"
    >
        {{-- Sticky Save Banner --}}
        <div
            x-show="hasUnsavedChanges && canEdit"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            class="sticky top-20 z-50 rounded-lg border-2 border-warning-500 bg-warning-50 p-4 shadow-lg dark:border-warning-400 dark:bg-warning-900"
            style="display: none;"
        >
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <svg class="h-6 w-6 text-warning-600 dark:text-warning-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                    <div>
                        <p class="font-semibold text-warning-900 dark:text-warning-100">You have unsaved changes</p>
                        <p class="text-sm text-warning-700 dark:text-warning-300">Save your changes or they will be lost</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button
                        @click="saveChanges()"
                        :disabled="isSaving"
                        class="inline-flex items-center gap-2 rounded-lg bg-primary-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition-all hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        <span x-text="isSaving ? 'Saving...' : 'Save Changes'"></span>
                    </button>
                    <button
                        @click="discardChanges()"
                        :disabled="isSaving"
                        class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition-all hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
                    >
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Discard
                    </button>
                </div>
            </div>
        </div>

        {{-- Hierarchical Tree --}}
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Service Request Types & Categories</h2>

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
            </div>
        </div>

        {{-- Bottom Action Buttons --}}
        <div class="space-y-4" x-show="canEdit">
            {{-- Add Category/Type Buttons --}}
            <div class="flex gap-4">
                {{-- Add Category Button --}}
                <div class="flex-1">
                    <button
                        class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
                        id="show-category-btn"
                        type="button"
                    >
                        <svg
                            class="h-5 w-5"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 4.5v15m7.5-7.5h-15"
                            />
                        </svg>
                        Add Category
                    </button>
                    <div
                        class="flex gap-2"
                        id="category-input-form"
                        style="display: none;"
                    >
                        <input
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                            id="new-category-name"
                            type="text"
                            placeholder="Category name..."
                        />
                        <button
                            class="inline-flex items-center rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500"
                            id="create-category-btn"
                            type="button"
                        >
                            Add
                        </button>
                        <button
                            class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
                            id="cancel-category-btn"
                            type="button"
                        >
                            Cancel
                        </button>
                    </div>
                </div>

                {{-- Add Type Button --}}
                <div class="flex-1">
                    <button
                        class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
                        id="show-type-btn"
                        type="button"
                    >
                        <svg
                            class="h-5 w-5"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 4.5v15m7.5-7.5h-15"
                            />
                        </svg>
                        Add Type
                    </button>
                    <div
                        class="flex gap-2"
                        id="type-input-form"
                        style="display: none;"
                    >
                        <input
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                            id="new-type-name"
                            type="text"
                            placeholder="Type name..."
                        />
                        <button
                            class="inline-flex items-center rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500"
                            id="create-type-btn"
                            type="button"
                        >
                            Add
                        </button>
                        <button
                            class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
                            id="cancel-type-btn"
                            type="button"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            </div>

            {{-- Save/Discard Buttons --}}
            <div class="flex justify-center gap-4">
                <button
                    class="inline-flex items-center gap-2 rounded-lg bg-primary-600 px-6 py-2 text-sm font-medium text-white shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 disabled:cursor-not-allowed disabled:opacity-50"
                    id="save-changes-btn"
                    type="button"
                    disabled
                >
                    <svg
                        class="h-4 w-4"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M4.5 12.75l6 6 9-13.5"
                        />
                    </svg>
                    Save Changes
                </button>
                <button
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-6 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
                    id="discard-changes-btn"
                    type="button"
                    disabled
                >
                    <svg
                        class="h-4 w-4"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                    Discard Changes
                </button>
            </div>
        </div>
    </div>

    @assets
        <style>
            /* Custom styles that cannot be replicated with Tailwind */
            .drop-line {
                position: absolute;
                left: 0;
                right: 0;
                height: 2px;
                background: rgb(59 130 246);
                border-radius: 1px;
                box-shadow: 0 0 6px rgba(59, 130, 246, 0.5);
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
                background: rgb(59 130 246);
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
                background: rgb(59 130 246);
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

            /* Ensure sortable containers support absolute positioned insertion lines */
            [data-sortable="categories"],
            [data-sortable="types"] {
                position: relative;
            }
        </style>
    @endassets

    @script
        <script>
            Alpine.data('serviceRequestTypeManager', () => ({
                originalTreeData: @json($this->hierarchicalData),
                treeData: @json($this->hierarchicalData),
                canEdit: @json($this->canEdit),
                categoryInputs: {},
                typeInputs: {},
                renamingCategories: {},
                renamingTypes: {},
                hasUnsavedChanges: false,
                isSaving: false,
                nextTempId: 1,
                dragData: {
                    isDragging: false,
                    draggedElement: null,
                    draggedType: null, // 'category' or 'type'
                    draggedId: null,
                    ghostElement: null,
                    currentDropTarget: null,
                    insertPosition: null, // 'before', 'after', or 'inside'
                },

                init() {
                    this.deepCopyTreeData();
                    this.render();
                    this.attachEventListeners();
                    this.setupDragAndDrop();
                },

                deepCopyTreeData() {
                    // Make a deep copy of the original data so we can track changes
                    this.treeData = JSON.parse(JSON.stringify(this.originalTreeData));
                    this.deletedCategories = [];
                    this.deletedTypes = [];
                    this.renamingCategories = {};
                    this.renamingTypes = {};
                },

                markAsChanged() {
                    this.hasUnsavedChanges = true;
                },

                render() {
                    this.renderCategories();
                    this.renderUncategorizedTypes();
                    this.updateSaveButton();

                    // Setup drag and drop after all DOM elements are rendered
                    this.setupDragAndDrop();
                },

                updateSaveButton() {
                    const saveButton = document.getElementById('save-changes-btn');
                    const discardButton = document.getElementById('discard-changes-btn');

                    if (saveButton && discardButton) {
                        if (this.hasUnsavedChanges) {
                            saveButton.disabled = false;
                            discardButton.disabled = false;
                            saveButton.classList.remove('opacity-50', 'cursor-not-allowed');
                            discardButton.classList.remove('opacity-50', 'cursor-not-allowed');
                        } else {
                            saveButton.disabled = true;
                            discardButton.disabled = true;
                            saveButton.classList.add('opacity-50', 'cursor-not-allowed');
                            discardButton.classList.add('opacity-50', 'cursor-not-allowed');
                        }
                    }
                },

                renderCategories() {
                    const container = document.getElementById('root-categories');
                    if (!container) return;

                    container.innerHTML = '';

                    if (this.treeData.categories && this.treeData.categories.length > 0) {
                        this.treeData.categories.forEach(category => {
                            const html = this.renderCategoryRecursive(category, 0);
                            const wrapper = document.createElement('div');
                            wrapper.innerHTML = html.trim();
                            const element = wrapper.firstElementChild;
                            if (element) {
                                container.appendChild(element);
                            }
                        });
                    }
                },

                renderUncategorizedTypes() {
                    const container = document.getElementById('uncategorized-types');

                    if (!container) return;

                    container.innerHTML = '';

                    if (this.treeData.uncategorized_types && this.treeData.uncategorized_types.length > 0) {
                        this.treeData.uncategorized_types.forEach(type => {
                            const html = this.renderType(type);
                            const wrapper = document.createElement('div');
                            wrapper.innerHTML = html.trim();
                            const element = wrapper.firstElementChild;
                            if (element) {
                                container.appendChild(element);
                            }
                        });
                    }
                },

                renderType(type) {
                    const requestCount = typeof type.service_requests_count === 'number' ? type
                        .service_requests_count : 0;
                    const canDelete = requestCount === 0;
                    const isRenaming = this.renamingTypes[type.id] || false;

                    return `<div data-type-id="${type.id}" class="type-item ${this.canEdit ? 'draggable cursor-grab active:cursor-grabbing' : ''} flex items-center gap-2 rounded-lg border border-gray-200 bg-white p-2 dark:border-gray-600 dark:bg-gray-800 transition-all duration-150 ease-out" ${this.canEdit ? 'draggable="true"' : ''}>
                        ${this.canEdit ? `<svg class="type-handle h-4 w-4 cursor-grab text-gray-400 opacity-60 transition-all duration-150 ease-in-out hover:opacity-100 hover:text-primary-500 hover:scale-110 active:cursor-grabbing" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>` : ''}
                        <svg class="h-4 w-4 text-primary-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                        ${this.canEdit && isRenaming ? `
                            <input
                                id="rename-type-${type.id}"
                                type="text"
                                value="${this.escapeHtml(type.name)}"
                                class="flex-1 text-sm text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-primary-500 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-primary-500"
                            />
                            <button
                                type="button"
                                class="text-green-600 hover:text-green-800 disabled:opacity-50 disabled:cursor-not-allowed"
                                @click.stop="confirmTypeRename('${type.id}')"
                                id="confirm-rename-type-${type.id}"
                            >
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                            </button>
                        ` : `
                            <span class="flex-1 text-sm text-gray-700 dark:text-gray-300">${this.escapeHtml(type.name)}</span>
                            ${this.canEdit ? `<button type="button" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200" @click.stop="startTypeRename('${type.id}')" title="Rename">
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                </svg>
                            </button>` : ''}
                        `}
                        <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-semibold text-gray-600 dark:bg-gray-600 dark:text-gray-100">${requestCount}</span>
                        ${this.canEdit && canDelete ? `<button type="button" class="text-red-600 hover:text-red-800" @click.stop="stageTypeDeletion('${type.id}')">
                                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M6 18L18 6" />
                                            </svg>
                                        </button>` : ''}
                    </div>`;
                },

                renderCategoryRecursive(category, level) {
                    const indent = level * 24;
                    const showCategoryInput = this.categoryInputs[category.id] || false;
                    const showTypeInput = this.typeInputs[category.id] || false;
                    const canAddChildCategory = level < 1;
                    const isRenaming = this.renamingCategories[category.id] || false;

                    return `<div class="category-wrapper" data-category-id="${category.id}">
                        <div class="category-item ${this.canEdit ? 'draggable cursor-grab active:cursor-grabbing' : ''} flex items-center gap-2 rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-gray-600 dark:bg-gray-700 transition-all duration-150 ease-out hover:bg-gray-50 hover:-translate-y-px hover:shadow-lg dark:hover:bg-gray-600" style="margin-left: ${indent}px" ${this.canEdit ? 'draggable="true"' : ''} data-category-id="${category.id}">
                            ${this.canEdit ? `<svg class="category-handle h-5 w-5 cursor-grab text-gray-400 opacity-60 transition-all duration-150 ease-in-out hover:opacity-100 hover:text-primary-500 hover:scale-110 active:cursor-grabbing" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>` : ''}
                            <svg class="h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" />
                            </svg>
                            ${this.canEdit && isRenaming ? `
                                <input
                                    id="rename-category-${category.id}"
                                    type="text"
                                    value="${this.escapeHtml(category.name)}"
                                    class="flex-1 font-medium text-gray-900 dark:text-white bg-white dark:bg-gray-600 border border-primary-500 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-primary-500"
                                />
                                <button
                                    type="button"
                                    class="text-green-600 hover:text-green-800 disabled:opacity-50 disabled:cursor-not-allowed"
                                    @click.stop="confirmCategoryRename('${category.id}')"
                                    id="confirm-rename-category-${category.id}"
                                >
                                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                    </svg>
                                </button>
                            ` : `
                                <span class="flex-1 font-medium text-gray-900 dark:text-white">${this.escapeHtml(category.name)}</span>
                                ${this.canEdit ? `<button type="button" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200" @click.stop="startCategoryRename('${category.id}')" title="Rename">
                                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                    </svg>
                                </button>` : ''}
                            `}
                            ${this.canEdit && this.canDeleteCategory(category) ? `<button type="button" class="text-red-600 hover:text-red-800" @click.stop="confirmDeleteCategory('${category.id}')">
                                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M6 18L18 6" />
                                                </svg>
                                            </button>` : ''}
                            ${this.canEdit && canAddChildCategory ? `
                                            <button @click="showCategoryInput('${category.id}')" class="rounded p-1 text-gray-500 hover:bg-gray-200 hover:text-gray-700 dark:hover:bg-gray-600 dark:hover:text-gray-300" title="Add child category">
                                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 10.5v6m3-3H9m4.06-7.19l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" />
                                                </svg>
                                            </button>` : ''}
                            ${this.canEdit ? `<button @click="showTypeInput('${category.id}')" class="rounded p-1 text-gray-500 hover:bg-gray-200 hover:text-gray-700 dark:hover:bg-gray-600 dark:hover:text-gray-300" title="Add type">
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                </svg>
                            </button>` : ''}
                        </div>

                        ${this.canEdit && canAddChildCategory && showCategoryInput ? `
                                                    <div id="category-input-${category.id}" class="flex gap-2 mt-2" style="margin-left: ${indent + 24}px">
                                                        <input id="child-category-${category.id}" type="text" placeholder="Child category name..." class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white" />
                                                        <button @click="createCategory('${category.id}')" class="rounded-lg bg-primary-600 px-3 py-1 text-sm text-white hover:bg-primary-700">Add</button>
                                                        <button @click="hideCategoryInput('${category.id}')" class="rounded-lg border border-gray-300 bg-white px-3 py-1 text-sm text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">Cancel</button>
                                                    </div>
                                                ` : ''}

                        ${this.canEdit && showTypeInput ? `
                                                            <div id="type-input-${category.id}" class="flex gap-2 mt-2" style="margin-left: ${indent + 24}px">
                                                                <input id="child-type-${category.id}" type="text" placeholder="Type name..." class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white" />
                                                                <button @click="createType('${category.id}')" class="rounded-lg bg-primary-600 px-3 py-1 text-sm text-white hover:bg-primary-700">Add</button>
                                                                <button @click="hideTypeInput('${category.id}')" class="rounded-lg border border-gray-300 bg-white px-3 py-1 text-sm text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">Cancel</button>
                                                            </div>
                                                        ` : ''}

                        ${category.types && category.types.length > 0 ? `
                                                            <div data-sortable="types" data-category-id="${category.id}" class="mt-2 space-y-1 min-h-4 p-0.5 rounded transition-colors duration-150 ease-in-out" style="margin-left: ${indent + 24}px">
                                                                ${category.types.map(type => this.renderType(type)).join('')}
                                                            </div>
                                                        ` : ''}

                        ${category.children && category.children.length > 0 ? `
                                                            <div data-sortable="categories" data-parent-id="${category.id}" class="mt-2 space-y-2 min-h-5 p-0.5 rounded-md transition-colors duration-150 ease-in-out">
                                                                ${category.children.map(child => this.renderCategoryRecursive(child, level + 1)).join('')}
                                                            </div>
                                                        ` : ''}
                    </div>`;
                },

                attachEventListeners() {
                    // Show category input button
                    document.getElementById('show-category-btn')?.addEventListener('click', () => {
                        document.getElementById('show-category-btn').style.display = 'none';
                        document.getElementById('category-input-form').style.display = 'flex';
                        document.getElementById('new-category-name')?.focus();
                    });

                    // Cancel category button
                    document.getElementById('cancel-category-btn')?.addEventListener('click', () => {
                        document.getElementById('show-category-btn').style.display = 'block';
                        document.getElementById('category-input-form').style.display = 'none';
                        document.getElementById('new-category-name').value = '';
                    });

                    // Create category button
                    document.getElementById('create-category-btn')?.addEventListener('click', () => {
                        this.createCategory(null);
                    });

                    // Category input enter key
                    document.getElementById('new-category-name')?.addEventListener('keydown', (e) => {
                        if (e.key === 'Enter') {
                            this.createCategory(null);
                        } else if (e.key === 'Escape') {
                            document.getElementById('cancel-category-btn')?.click();
                        }
                    });

                    // Show type input button
                    document.getElementById('show-type-btn')?.addEventListener('click', () => {
                        document.getElementById('show-type-btn').style.display = 'none';
                        document.getElementById('type-input-form').style.display = 'flex';
                        document.getElementById('new-type-name')?.focus();
                    });

                    // Cancel type button
                    document.getElementById('cancel-type-btn')?.addEventListener('click', () => {
                        document.getElementById('show-type-btn').style.display = 'block';
                        document.getElementById('type-input-form').style.display = 'none';
                        document.getElementById('new-type-name').value = '';
                    });

                    // Create type button
                    document.getElementById('create-type-btn')?.addEventListener('click', () => {
                        this.createType(null);
                    });

                    // Type input enter key
                    document.getElementById('new-type-name')?.addEventListener('keydown', (e) => {
                        if (e.key === 'Enter') {
                            this.createType(null);
                        } else if (e.key === 'Escape') {
                            document.getElementById('cancel-type-btn')?.click();
                        }
                    });

                    // Save changes button
                    document.getElementById('save-changes-btn')?.addEventListener('click', () => {
                        this.saveChanges();
                    });

                    // Discard changes button
                    document.getElementById('discard-changes-btn')?.addEventListener('click', () => {
                        this.discardChanges();
                    });
                },

                setupDragAndDrop() {
                    // Skip drag and drop setup if user doesn't have edit permission
                    if (!this.canEdit) {
                        return;
                    }

                    // Use a small timeout to ensure DOM elements are fully rendered
                    setTimeout(() => {
                        const draggableElements = document.querySelectorAll('.draggable');

                        // Add event listeners to all draggable elements
                        draggableElements.forEach(el => {
                            // Remove existing listeners first
                            if (el._dragStartHandler) {
                                el.removeEventListener('dragstart', el._dragStartHandler);
                            }
                            if (el._dragEndHandler) {
                                el.removeEventListener('dragend', el._dragEndHandler);
                            }

                            // Create bound handlers and store references
                            el._dragStartHandler = this.handleDragStart.bind(this);
                            el._dragEndHandler = this.handleDragEnd.bind(this);

                            // Add event listeners
                            el.addEventListener('dragstart', el._dragStartHandler);
                            el.addEventListener('dragend', el._dragEndHandler);

                            // Verify draggable attribute
                            if (!el.draggable) {
                                el.draggable = true;
                            }
                        });

                        // Add event listeners for drop zones
                        document.querySelectorAll('.category-item, .type-item').forEach(el => {
                            // Remove existing listeners first
                            if (el._dragOverHandler) {
                                el.removeEventListener('dragover', el._dragOverHandler);
                            }
                            if (el._dropHandler) {
                                el.removeEventListener('drop', el._dropHandler);
                            }
                            if (el._dragEnterHandler) {
                                el.removeEventListener('dragenter', el._dragEnterHandler);
                            }
                            if (el._dragLeaveHandler) {
                                el.removeEventListener('dragleave', el._dragLeaveHandler);
                            }

                            // Create bound handlers and store references
                            el._dragOverHandler = this.handleDragOver.bind(this);
                            el._dropHandler = this.handleDrop.bind(this);
                            el._dragEnterHandler = this.handleDragEnter.bind(this);
                            el._dragLeaveHandler = this.handleDragLeave.bind(this);

                            // Add event listeners
                            el.addEventListener('dragover', el._dragOverHandler);
                            el.addEventListener('drop', el._dropHandler);
                            el.addEventListener('dragenter', el._dragEnterHandler);
                            el.addEventListener('dragleave', el._dragLeaveHandler);
                        });

                        document.querySelectorAll('[data-sortable="types"]').forEach(container => {
                            if (container._dragOverHandler) {
                                container.removeEventListener('dragover', container
                                    ._dragOverHandler);
                            }
                            if (container._dropHandler) {
                                container.removeEventListener('drop', container._dropHandler);
                            }
                            if (container._dragEnterHandler) {
                                container.removeEventListener('dragenter', container
                                    ._dragEnterHandler);
                            }
                            if (container._dragLeaveHandler) {
                                container.removeEventListener('dragleave', container
                                    ._dragLeaveHandler);
                            }

                            container._dragOverHandler = this.handleDragOver.bind(this);
                            container._dropHandler = this.handleDrop.bind(this);
                            container._dragEnterHandler = this.handleDragEnter.bind(this);
                            container._dragLeaveHandler = this.handleDragLeave.bind(this);

                            container.addEventListener('dragover', container._dragOverHandler);
                            container.addEventListener('drop', container._dropHandler);
                            container.addEventListener('dragenter', container._dragEnterHandler);
                            container.addEventListener('dragleave', container._dragLeaveHandler);
                        });
                    }, 100);
                },

                handleDragStart(e) {

                    this.dragData.isDragging = true;
                    this.dragData.draggedElement = e.target;

                    // Determine what type of element is being dragged
                    if (e.target.dataset.categoryId) {
                        this.dragData.draggedType = 'category';
                        this.dragData.draggedId = e.target.dataset.categoryId;
                    } else if (e.target.dataset.typeId) {
                        this.dragData.draggedType = 'type';
                        this.dragData.draggedId = e.target.dataset.typeId;
                    }

                    // Add dragging visual feedback with Tailwind classes (NO pointer-events-none during drag!)
                    e.target.classList.add('opacity-50', 'rotate-1', 'scale-105', 'z-50', 'shadow-2xl');


                    // If dragging a category, also add subtle visual feedback to the entire wrapper to show children will move
                    if (this.dragData.draggedType === 'category') {
                        const wrapper = e.target.closest('.category-wrapper');
                        if (wrapper) {
                            // Add a subtle outline to the entire wrapper to indicate all children will move
                            wrapper.classList.add('ring-2', 'ring-primary-300', 'ring-opacity-50');

                        }
                    }

                    // Set drag data
                    e.dataTransfer.effectAllowed = 'move';
                    e.dataTransfer.setData('text/plain', this.dragData.draggedId);

                    // Create a transparent drag image to hide the default ghost
                    const dragImage = document.createElement('div');
                    dragImage.style.cssText = 'width: 1px; height: 1px; opacity: 0;';
                    document.body.appendChild(dragImage);
                    e.dataTransfer.setDragImage(dragImage, 0, 0);
                    setTimeout(() => document.body.removeChild(dragImage), 0);

                    // Create custom ghost element
                    this.createDragGhost(e.target);

                    // Add mousemove listener for ghost positioning
                    document.addEventListener('dragover', this.updateGhostPosition.bind(this));
                },

                handleDragEnd(e) {

                    this.dragData.isDragging = false;

                    // Remove all drag visual classes
                    e.target.classList.remove('opacity-50', 'rotate-1', 'scale-105', 'z-50', 'shadow-2xl');

                    // Also remove any updating classes that might be stuck
                    e.target.classList.remove('updating-order', 'opacity-70');

                    // Remove wrapper visual feedback for categories
                    if (this.dragData.draggedType === 'category') {
                        const wrapper = e.target.closest('.category-wrapper');
                        if (wrapper) {
                            wrapper.classList.remove('ring-2', 'ring-primary-300', 'ring-opacity-50');
                        }
                    }

                    // Find the actual dragged element (might be different due to DOM manipulation)
                    if (this.dragData.draggedElement && this.dragData.draggedElement !== e.target) {
                        this.dragData.draggedElement.classList.remove('opacity-50', 'rotate-1', 'scale-105', 'z-50',
                            'shadow-2xl', 'updating-order', 'opacity-70');

                        // Also clean wrapper for moved elements
                        if (this.dragData.draggedType === 'category') {
                            const wrapper = this.dragData.draggedElement.closest('.category-wrapper');
                            if (wrapper) {
                                wrapper.classList.remove('ring-2', 'ring-primary-300', 'ring-opacity-50');
                            }
                        }
                    }

                    // Clean up visual feedback
                    this.cleanupDragVisuals();

                    // Remove ghost element
                    if (this.dragData.ghostElement) {
                        this.dragData.ghostElement.remove();
                        this.dragData.ghostElement = null;
                    }
                },

                handleDragOver(e) {
                    e.preventDefault();

                    if (!this.dragData.isDragging) {
                        return;
                    }

                    // Check if this is a valid drop zone
                    const dropPosition = this.determineDropPosition(e.currentTarget, e);

                    if (dropPosition) {
                        e.dataTransfer.dropEffect = 'move';
                    } else {
                        e.dataTransfer.dropEffect = 'none';
                    }

                    // Update ghost position
                    this.updateGhostPosition(e);

                    // Update drop indicators
                    this.updateDropIndicators(e);
                },

                handleDragEnter(e) {
                    e.preventDefault();
                    if (!this.dragData.isDragging) return;

                    this.updateDropIndicators(e);
                },

                handleDragLeave(e) {
                    // Only clean up if we're actually leaving the element
                    if (!e.currentTarget.contains(e.relatedTarget)) {
                        this.cleanupDropIndicators();
                    }
                },

                handleDrop(e) {
                    e.preventDefault();


                    if (!this.dragData.isDragging) return;

                    const dropTarget = e.currentTarget;
                    const dropPosition = this.determineDropPosition(dropTarget, e);



                    // If no valid drop position, cancel the drop
                    if (!dropPosition) {

                        this.cleanupDragVisuals();
                        return;
                    }

                    this.performDrop(dropTarget, dropPosition);

                    // Clean up drag visuals immediately
                    this.cleanupDragVisuals();

                    // Remove drag classes from the original element immediately
                    if (this.dragData.draggedElement) {
                        this.dragData.draggedElement.classList.remove('opacity-50', 'rotate-1', 'scale-105', 'z-50',
                            'shadow-2xl', 'updating-order', 'opacity-70');

                    }

                    // Also clean up any elements with the dragged ID (in case DOM was manipulated)
                    if (this.dragData.draggedType === 'category' && this.dragData.draggedId) {
                        const categoryElement = document.querySelector(
                            `[data-category-id="${this.dragData.draggedId}"]`);
                        if (categoryElement) {
                            categoryElement.classList.remove('opacity-50', 'rotate-1', 'scale-105', 'z-50',
                                'shadow-2xl', 'updating-order', 'opacity-70');

                            // Also clean wrapper classes
                            const wrapper = categoryElement.closest('.category-wrapper');
                            if (wrapper) {
                                wrapper.classList.remove('ring-2', 'ring-primary-300', 'ring-opacity-50');
                            }
                        }
                    } else if (this.dragData.draggedType === 'type' && this.dragData.draggedId) {
                        const typeElement = document.querySelector(`[data-type-id="${this.dragData.draggedId}"]`);
                        if (typeElement) {
                            typeElement.classList.remove('opacity-50', 'rotate-1', 'scale-105', 'z-50',
                                'shadow-2xl', 'updating-order', 'opacity-70');
                        }
                    }
                },

                createDragGhost(element) {


                    // Create a simplified ghost element
                    const ghost = document.createElement('div');
                    ghost.className = 'drag-ghost';
                    const spanElement = element.querySelector('span');
                    ghost.textContent = spanElement ? spanElement.textContent : 'Dragging...';
                    ghost.style.cssText = `
                    position: fixed;
                    pointer-events: none;
                    z-index: 9999;
                    background: rgba(59, 130, 246, 0.9);
                    color: white;
                    padding: 8px 12px;
                    border-radius: 6px;
                    font-size: 14px;
                    font-weight: 500;
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                    backdrop-filter: blur(4px);
                    left: -9999px;
                    top: -9999px;
                `;

                    document.body.appendChild(ghost);
                    this.dragData.ghostElement = ghost;

                },

                updateGhostPosition(e) {
                    if (this.dragData.ghostElement) {
                        const x = e.clientX + 15;
                        const y = e.clientY - 10;
                        this.dragData.ghostElement.style.left = x + 'px';
                        this.dragData.ghostElement.style.top = y + 'px';

                        // Log occasionally to see positioning

                    }
                },

                updateDropIndicators(e) {
                    this.cleanupDropIndicators();

                    const target = e.currentTarget;
                    const dropPosition = this.determineDropPosition(target, e);

                    // If no valid drop position, show no visual feedback
                    if (!dropPosition) {
                        return;
                    }

                    if (dropPosition.type === 'inside') {
                        // Highlight category for nesting with Tailwind classes
                        if (dropPosition.target.classList.contains('category-item')) {
                            dropPosition.target.classList.add('nest-target', 'bg-primary-500/10',
                                'border-primary-500/50', 'border-2', 'rounded-lg', 'relative');
                        }
                    } else if (dropPosition.type === 'insert') {
                        // Show single insertion line at calculated position
                        this.showInsertionLine(dropPosition.container, dropPosition.insertIndex);
                    }
                },

                showInsertionLine(container, insertIndex) {
                    // Remove any existing insertion lines
                    this.cleanupInsertionLines();

                    // Get all children of the container
                    const children = Array.from(container.children).filter(child =>
                        !child.classList.contains('insertion-line') &&
                        (child.classList.contains('category-wrapper') || child.classList.contains(
                            'category-item') || child.classList.contains('type-item'))
                    );

                    // Calculate the Y position for the insertion line
                    let yPosition = 0;
                    const containerRect = container.getBoundingClientRect();

                    if (insertIndex === 0 && children.length > 0) {
                        // Position above the first child
                        const firstChildRect = children[0].getBoundingClientRect();
                        yPosition = firstChildRect.top - containerRect.top - 1;
                    } else if (insertIndex >= children.length && children.length > 0) {
                        // Position below the last child
                        const lastChildRect = children[children.length - 1].getBoundingClientRect();
                        yPosition = lastChildRect.bottom - containerRect.top + 1;
                    } else if (children.length > 0 && insertIndex > 0) {
                        // Position between children
                        const prevChildRect = children[insertIndex - 1].getBoundingClientRect();
                        const nextChildRect = children[insertIndex].getBoundingClientRect();
                        yPosition = prevChildRect.bottom - containerRect.top + ((nextChildRect.top - prevChildRect
                            .bottom) / 2);
                    } else {
                        // Empty container or single item
                        yPosition = 10;
                    }

                    // Create insertion line with absolute positioning
                    const line = document.createElement('div');
                    line.className = 'insertion-line drop-line';
                    line.style.top = yPosition + 'px';



                    container.appendChild(line);
                },

                cleanupInsertionLines() {
                    document.querySelectorAll('.insertion-line').forEach(line => line.remove());
                },

                calculateInsertionPosition(container, mouseY) {
                    const children = Array.from(container.children).filter(child =>
                        !child.classList.contains('insertion-line') &&
                        (child.classList.contains('category-wrapper') || child.classList.contains(
                            'category-item') || child.classList.contains('type-item'))
                    );

                    if (children.length === 0) {
                        return 0;
                    }

                    // Find the insertion position based on mouse Y coordinate
                    for (let i = 0; i < children.length; i++) {
                        const rect = children[i].getBoundingClientRect();
                        const childCenterY = rect.top + (rect.height / 2);

                        if (mouseY < childCenterY) {
                            return i;
                        }
                    }

                    // If we're past all children, insert at the end
                    return children.length;
                },

                determineDropPosition(target, e) {
                    // Ensure we're working with the actual category-item or type-item element
                    const categoryItem = target.closest('.category-item');
                    const typeItem = target.closest('.type-item');

                    if (categoryItem && this.dragData.draggedType === 'category') {
                        const actualTarget = categoryItem;
                        const draggedLevel = this.getCategoryLevel(this.dragData.draggedElement);
                        const targetLevel = this.getCategoryLevel(actualTarget);

                        // Check if they share the same parent context
                        const draggedParentContext = this.getCategoryParentContext(this.dragData.draggedElement);
                        const targetParentContext = this.getCategoryParentContext(actualTarget);

                        // Determine if insertion lines should be shown
                        let allowInsertion = false;

                        if (targetParentContext === 'root') {
                            // Target is at root level - always allow insertion (can move anything to root)
                            allowInsertion = true;

                        } else if (draggedLevel === targetLevel && draggedParentContext === targetParentContext) {
                            // Same level and same parent context - allow insertion
                            allowInsertion = true;

                        } else if (draggedLevel === targetLevel) {
                            // Same level but different parents - could be valid reordering
                            allowInsertion = true;

                        } else {

                        }

                        if (allowInsertion) {
                            const targetWrapper = actualTarget.closest('.category-wrapper');
                            const container = targetWrapper.parentElement;
                            const rect = actualTarget.getBoundingClientRect();
                            const y = e.clientY - rect.top;
                            const height = rect.height;

                            if (y > height * 0.3 && y < height * 0.7) {
                                const prospectiveParentId = actualTarget.dataset.categoryId;
                                if (this.draggingCategoryWouldViolateDepth(this.dragData.draggedId,
                                        prospectiveParentId) || this.wouldExceedDepthLimit(actualTarget)) {
                                    return null;
                                }

                                return {
                                    type: 'inside',
                                    target: actualTarget,
                                };
                            } else {
                                // Calculate insertion position based on mouse Y
                                const insertIndex = this.calculateInsertionPosition(container, e.clientY);
                                const parentId = container?.dataset?.parentId || null;
                                if (this.wouldExceedDepthLimit(null, parentId)) {
                                    return null;
                                }
                                return {
                                    type: 'insert',
                                    container: container,
                                    insertIndex: insertIndex
                                };
                            }
                        } else {
                            const prospectiveParentId = actualTarget.dataset.categoryId;
                            if (this.draggingCategoryWouldViolateDepth(this.dragData.draggedId,
                                    prospectiveParentId) || this.wouldExceedDepthLimit(actualTarget)) {
                                return null;
                            }

                            return {
                                type: 'inside',
                                target: actualTarget,
                            };
                        }
                    }

                    if (typeItem && this.dragData.draggedType === 'type') {
                        const actualTarget = typeItem;
                        const draggedContainer = this.dragData.draggedElement.closest('[data-sortable="types"]');
                        const targetContainer = actualTarget.closest('[data-sortable="types"]');

                        if (draggedContainer && targetContainer &&
                            draggedContainer.dataset.categoryId === targetContainer.dataset.categoryId) {
                            const insertIndex = this.calculateInsertionPosition(targetContainer, e.clientY);
                            return {
                                type: 'insert',
                                container: targetContainer,
                                insertIndex: insertIndex
                            };
                        } else {
                            return null;
                        }
                    }

                    if (this.dragData.draggedType === 'type') {
                        const typeContainer = target.closest('[data-sortable="types"]');

                        if (typeContainer) {
                            const draggedContainer = this.dragData.draggedElement?.closest(
                                '[data-sortable="types"]');
                            const containerCategoryId = typeContainer.dataset.categoryId || null;
                            const draggedCategoryId = draggedContainer?.dataset?.categoryId || null;
                            const sameContainer = draggedContainer === typeContainer;
                            const targetIsUncategorized = !typeContainer.dataset.categoryId;

                            if (sameContainer || targetIsUncategorized) {
                                const insertIndex = this.calculateInsertionPosition(typeContainer, e.clientY);
                                return {
                                    type: 'insert',
                                    container: typeContainer,
                                    insertIndex,
                                };
                            }

                            return null;
                        }
                    }

                    if (categoryItem && this.dragData.draggedType === 'type') {
                        return {
                            type: 'inside',
                            target: categoryItem
                        };
                    }

                    return null; // Invalid drop
                },

                cleanupDropIndicators() {
                    this.cleanupInsertionLines();
                    document.querySelectorAll('.nest-target').forEach(target => {
                        target.classList.remove('nest-target', 'bg-primary-500/10', 'border-primary-500/50',
                            'border-2', 'rounded-lg', 'relative');
                    });
                },

                cleanupDragVisuals() {
                    this.cleanupDropIndicators();
                    document.removeEventListener('dragover', this.updateGhostPosition);
                },

                getParentIdFromPosition(position) {
                    if (position.type === 'inside') {
                        return position.target?.dataset?.categoryId || null;
                    }

                    if (position.type === 'insert') {
                        const parentId = position.container?.dataset?.parentId;
                        return parentId && parentId !== '' ? parentId : null;
                    }

                    return null;
                },

                draggingCategoryWouldViolateDepth(categoryId, newParentId) {
                    if (!categoryId) {
                        return false;
                    }

                    const movedCategory = this.findCategoryById(categoryId);
                    if (!movedCategory) {
                        return false;
                    }

                    const hasChildren = Array.isArray(movedCategory.children) && movedCategory.children.length > 0;

                    if (!hasChildren) {
                        return false;
                    }

                    if (!newParentId) {
                        return false;
                    }

                    const prospectiveParentElement = document.querySelector(`[data-category-id="${newParentId}"]`);
                    if (!prospectiveParentElement) {
                        return false;
                    }

                    return this.getCategoryLevel(prospectiveParentElement) >= 0;
                },

                performDrop(target, position) {
                    if (this.dragData.draggedType === 'category') {
                        const prospectiveParentId = this.getParentIdFromPosition(position);
                        if (this.draggingCategoryWouldViolateDepth(this.dragData.draggedId, prospectiveParentId)) {
                            return;
                        }

                        this.handleCategoryDrop(target, position);
                    } else if (this.dragData.draggedType === 'type') {
                        this.handleTypeDrop(target, position);
                    }

                    // Mark as changed and re-render
                    this.markAsChanged();
                    this.render();
                },

                handleCategoryDrop(target, position) {
                    const categoryId = this.dragData.draggedId;
                    let newParentId = null;

                    if (position.type === 'inside') {
                        newParentId = position.target.dataset.categoryId;
                    } else if (position.type === 'insert') {
                        newParentId = position.container.dataset.parentId || null;
                        if (newParentId === '') {
                            newParentId = null;
                        }
                    }

                    // Update the tree data structure
                    this.updateCategoryInTreeData(categoryId, newParentId, position);
                },

                handleTypeDrop(target, position) {
                    const typeId = this.dragData.draggedId;
                    let newCategoryId = null;

                    if (position.type === 'inside') {
                        newCategoryId = position.target.dataset.categoryId;
                    } else if (position.type === 'insert') {
                        newCategoryId = position.container.dataset.categoryId || null;
                    }

                    // Update the tree data structure
                    this.updateTypeInTreeData(typeId, newCategoryId, position);
                },

                wouldExceedDepthLimit(targetElement, parentId = null) {
                    if (targetElement) {
                        return this.getCategoryLevel(targetElement) >= 1;
                    }

                    if (parentId) {
                        const parentCategory = document.querySelector(`[data-category-id="${parentId}"]`);
                        if (parentCategory) {
                            return this.getCategoryLevel(parentCategory) >= 1;
                        }
                    }

                    return false;
                },

                updateCategoryInTreeData(categoryId, newParentId, position) {
                    const category = this.findAndRemoveCategory(categoryId);
                    if (!category) return;

                    category.parent_id = newParentId;

                    if (newParentId) {
                        const parentCategory = this.findCategoryById(newParentId);
                        if (parentCategory) {
                            parentCategory.children = parentCategory.children || [];
                            if (position.type === 'inside') {
                                parentCategory.children.push(category);
                            } else {
                                parentCategory.children.splice(position.insertIndex, 0, category);
                            }
                        }
                    } else {
                        this.treeData.categories = this.treeData.categories || [];
                        if (position.type === 'insert') {
                            this.treeData.categories.splice(position.insertIndex, 0, category);
                        } else {
                            this.treeData.categories.push(category);
                        }
                    }
                },

                updateTypeInTreeData(typeId, newCategoryId, position) {
                    const type = this.findAndRemoveType(typeId);
                    if (!type) return;

                    type.category_id = newCategoryId;

                    if (newCategoryId) {
                        const category = this.findCategoryById(newCategoryId);
                        if (category) {
                            category.types = category.types || [];
                            if (position.type === 'inside') {
                                category.types.push(type);
                            } else {
                                category.types.splice(position.insertIndex, 0, type);
                            }
                        }
                    } else {
                        this.treeData.uncategorized_types = this.treeData.uncategorized_types || [];
                        if (position.type === 'insert') {
                            this.treeData.uncategorized_types.splice(position.insertIndex, 0, type);
                        } else {
                            this.treeData.uncategorized_types.push(type);
                        }
                    }
                },

                findCategoryParent(categoryId) {
                    const findParent = (categories, targetId, parentId = null) => {
                        for (const category of categories) {
                            if (category.id === targetId) {
                                return parentId;
                            }
                            if (category.children && category.children.length > 0) {
                                const result = findParent(category.children, targetId, category.id);
                                if (result !== undefined) return result;
                            }
                        }
                        return undefined;
                    };

                    return findParent(this.treeData.categories || [], categoryId);
                },

                findTypeCategory(typeId) {
                    const findCategory = (categories) => {
                        for (const category of categories) {
                            if (category.types && category.types.some(type => type.id === typeId)) {
                                return category.id;
                            }
                            if (category.children && category.children.length > 0) {
                                const result = findCategory(category.children);
                                if (result) return result;
                            }
                        }
                        return null;
                    };

                    const categoryResult = findCategory(this.treeData.categories || []);
                    if (categoryResult) return categoryResult;

                    if (this.treeData.uncategorized_types &&
                        this.treeData.uncategorized_types.some(type => type.id === typeId)) {
                        return null;
                    }

                    return null;
                },

                getCategoryLevel(categoryElement) {
                    let level = 0;
                    let current = categoryElement.closest('.category-wrapper');

                    while (current && current.parentElement) {
                        const parent = current.parentElement.closest('[data-sortable="categories"]');

                        if (parent && parent.dataset.parentId) {
                            level++;
                            current = parent.closest('.category-wrapper');
                        } else {
                            break;
                        }
                    }

                    return level;
                },

                getCategoryParentContext(categoryElement) {
                    const wrapper = categoryElement.closest('.category-wrapper');
                    if (!wrapper) {
                        return 'root';
                    }

                    const parentContainer = wrapper.parentElement.closest('[data-sortable="categories"]');
                    if (!parentContainer) {
                        return 'root';
                    }

                    const parentId = parentContainer.dataset.parentId;
                    return parentId && parentId !== 'null' && parentId !== '' ? parentId : 'root';
                },

                findCategoryById(categoryId, categories = null) {
                    if (!categories) {
                        categories = this.treeData.categories || [];
                    }

                    for (const category of categories) {
                        if (category.id === categoryId) {
                            return category;
                        }
                        if (category.children) {
                            const found = this.findCategoryById(categoryId, category.children);
                            if (found) return found;
                        }
                    }
                    return null;
                },

                findTypeById(typeId) {
                    // Check uncategorized types first
                    if (this.treeData.uncategorized_types) {
                        const type = this.treeData.uncategorized_types.find(t => t.id === typeId);
                        if (type) return type;
                    }

                    // Search in categories recursively
                    const findInCategories = (categories) => {
                        for (const category of categories) {
                            if (category.types) {
                                const type = category.types.find(t => t.id === typeId);
                                if (type) return type;
                            }
                            if (category.children) {
                                const found = findInCategories(category.children);
                                if (found) return found;
                            }
                        }
                        return null;
                    };

                    return findInCategories(this.treeData.categories || []);
                },

                findAndRemoveCategory(categoryId) {
                    const rootIndex = (this.treeData.categories || []).findIndex(c => c.id === categoryId);
                    if (rootIndex !== -1) {
                        return this.treeData.categories.splice(rootIndex, 1)[0];
                    }

                    return this.findAndRemoveCategoryRecursive(categoryId, this.treeData.categories || []);
                },

                findAndRemoveCategoryRecursive(categoryId, categories) {
                    for (const category of categories) {
                        if (category.children) {
                            const childIndex = category.children.findIndex(c => c.id === categoryId);
                            if (childIndex !== -1) {
                                return category.children.splice(childIndex, 1)[0];
                            }
                            const found = this.findAndRemoveCategoryRecursive(categoryId, category.children);
                            if (found) return found;
                        }
                    }
                    return null;
                },

                findAndRemoveType(typeId) {
                    const uncategorizedIndex = (this.treeData.uncategorized_types || []).findIndex(t => t.id ===
                        typeId);
                    if (uncategorizedIndex !== -1) {
                        return this.treeData.uncategorized_types.splice(uncategorizedIndex, 1)[0];
                    }

                    return this.findAndRemoveTypeRecursive(typeId, this.treeData.categories || []);
                },

                findAndRemoveTypeRecursive(typeId, categories) {
                    for (const category of categories) {
                        if (category.types) {
                            const typeIndex = category.types.findIndex(t => t.id === typeId);
                            if (typeIndex !== -1) {
                                return category.types.splice(typeIndex, 1)[0];
                            }
                        }
                        if (category.children) {
                            const found = this.findAndRemoveTypeRecursive(typeId, category.children);
                            if (found) return found;
                        }
                    }
                    return null;
                },

                async saveChanges() {
                    if (!this.hasUnsavedChanges || this.isSaving) return;

                    this.isSaving = true;

                    try {
                        // Prepare the tree data for saving with new items separated
                        const saveData = this.prepareSaveData();
                        await $wire.saveChanges(saveData);

                        // The server will clear the cached data, so we can just wait a moment
                        // and then get the fresh data
                        await new Promise(resolve => setTimeout(resolve, 100));

                        // Get fresh data from the server
                        const freshData = await $wire.call('getHierarchicalData');
                        this.originalTreeData = freshData;
                        this.treeData = JSON.parse(JSON.stringify(this.originalTreeData));
                        this.hasUnsavedChanges = false;
                        this.render();
                    } catch (error) {
                        console.error('Save failed:', error);
                    } finally {
                        this.isSaving = false;
                    }
                },

                prepareSaveData() {
                    const newCategories = [];
                    const newTypes = [];
                    const updatedCategories = [];
                    const updatedTypes = [];

                    this.deletedCategories = this.deletedCategories || [];
                    this.deletedTypes = this.deletedTypes || [];

                    this.updateSortOrders();
                    this.extractNewItems(this.treeData.categories || [], newCategories, newTypes, null);
                    this.extractNewItemsFromUncategorized(newTypes);
                    this.extractUpdatedItems(this.treeData.categories || [], updatedCategories, updatedTypes);
                    this.extractUpdatedItemsFromUncategorized(updatedTypes);

                    return {
                        categories: this.treeData.categories || [],
                        uncategorized_types: this.treeData.uncategorized_types || [],
                        new_categories: newCategories,
                        new_types: newTypes,
                        updated_categories: updatedCategories,
                        updated_types: updatedTypes,
                        deleted_categories: this.deletedCategories,
                        deleted_types: this.deletedTypes,
                    };
                },

                updateSortOrders() {
                    // Update sort orders for root categories
                    if (this.treeData.categories) {
                        this.treeData.categories.forEach((category, index) => {
                            category.sort = index + 1;
                            category.parent_id = null; // Ensure root categories have null parent_id
                            this.updateCategorySortOrders(category);
                        });
                    }

                    // Update sort orders for uncategorized types
                    if (this.treeData.uncategorized_types) {
                        this.treeData.uncategorized_types.forEach((type, index) => {
                            type.sort = index + 1;
                            type.category_id = null; // Ensure uncategorized types have null category_id
                        });
                    }
                },

                updateCategorySortOrders(category) {
                    // Update sort orders for types in this category
                    if (category.types) {
                        category.types.forEach((type, index) => {
                            type.sort = index + 1;
                            type.category_id = category.id;
                        });
                    }

                    // Update sort orders for child categories
                    if (category.children) {
                        category.children.forEach((child, index) => {
                            child.sort = index + 1;
                            child.parent_id = category.id;
                            this.updateCategorySortOrders(child);
                        });
                    }
                },

                extractNewItems(categories, newCategories, newTypes, parentId) {
                    categories.forEach((category, index) => {
                        if (typeof category.id === 'string' && category.id.startsWith('temp_')) {
                            // This is a new category
                            newCategories.push({
                                temp_id: category.id,
                                name: category.name,
                                parent_id: parentId,
                                sort: index + 1
                            });
                        }

                        // Extract new types from this category
                        if (category.types) {
                            category.types.forEach((type, typeIndex) => {
                                if (typeof type.id === 'string' && type.id.startsWith('temp_')) {
                                    // This is a new type
                                    newTypes.push({
                                        temp_id: type.id,
                                        name: type.name,
                                        category_id: category.id,
                                        sort: typeIndex + 1
                                    });
                                }
                            });
                        }

                        // Recursively handle children
                        if (category.children) {
                            this.extractNewItems(category.children, newCategories, newTypes, category.id);
                        }
                    });
                },

                extractNewItemsFromUncategorized(newTypes) {
                    if (this.treeData.uncategorized_types) {
                        this.treeData.uncategorized_types.forEach((type, index) => {
                            if (typeof type.id === 'string' && type.id.startsWith('temp_')) {
                                newTypes.push({
                                    temp_id: type.id,
                                    name: type.name,
                                    category_id: null,
                                    sort: index + 1
                                });
                            }
                        });
                    }
                },

                extractUpdatedItems(categories, updatedCategories, updatedTypes) {
                    categories.forEach(category => {
                        // Only check existing categories (not temp ones)
                        if (!(typeof category.id === 'string' && category.id.startsWith('temp_'))) {
                            const originalCategory = this.findOriginalCategoryById(category.id);
                            if (originalCategory && originalCategory.name !== category.name) {
                                updatedCategories.push({
                                    id: category.id,
                                    name: category.name
                                });
                            }
                        }

                        // Check types in this category
                        if (category.types) {
                            category.types.forEach(type => {
                                if (!(typeof type.id === 'string' && type.id.startsWith('temp_'))) {
                                    const originalType = this.findOriginalTypeById(type.id);
                                    if (originalType && originalType.name !== type.name) {
                                        updatedTypes.push({
                                            id: type.id,
                                            name: type.name
                                        });
                                    }
                                }
                            });
                        }

                        // Recursively handle children
                        if (category.children) {
                            this.extractUpdatedItems(category.children, updatedCategories, updatedTypes);
                        }
                    });
                },

                extractUpdatedItemsFromUncategorized(updatedTypes) {
                    if (this.treeData.uncategorized_types) {
                        this.treeData.uncategorized_types.forEach(type => {
                            if (!(typeof type.id === 'string' && type.id.startsWith('temp_'))) {
                                const originalType = this.findOriginalTypeById(type.id);
                                if (originalType && originalType.name !== type.name) {
                                    updatedTypes.push({
                                        id: type.id,
                                        name: type.name
                                    });
                                }
                            }
                        });
                    }
                },

                findOriginalCategoryById(categoryId, categories = null) {
                    if (!categories) {
                        categories = this.originalTreeData.categories || [];
                    }

                    for (const category of categories) {
                        if (category.id === categoryId) {
                            return category;
                        }
                        if (category.children) {
                            const found = this.findOriginalCategoryById(categoryId, category.children);
                            if (found) return found;
                        }
                    }
                    return null;
                },

                findOriginalTypeById(typeId) {
                    // Check uncategorized types first
                    if (this.originalTreeData.uncategorized_types) {
                        const type = this.originalTreeData.uncategorized_types.find(t => t.id === typeId);
                        if (type) return type;
                    }

                    // Search in categories recursively
                    const findInCategories = (categories) => {
                        for (const category of categories) {
                            if (category.types) {
                                const type = category.types.find(t => t.id === typeId);
                                if (type) return type;
                            }
                            if (category.children) {
                                const found = findInCategories(category.children);
                                if (found) return found;
                            }
                        }
                        return null;
                    };

                    return findInCategories(this.originalTreeData.categories || []);
                },

                discardChanges() {
                    this.deepCopyTreeData();
                    this.hasUnsavedChanges = false;
                    this.categoryInputs = {};
                    this.typeInputs = {};
                    this.render();
                },

                showCategoryInput(categoryId) {
                    this.categoryInputs[categoryId] = true;
                    this.render();
                    setTimeout(() => {
                        document.getElementById(`child-category-${categoryId}`)?.focus();
                        this.attachInputKeyHandlers(categoryId, 'category');
                    }, 50);
                },

                hideCategoryInput(categoryId) {
                    this.categoryInputs[categoryId] = false;
                    this.render();
                },

                showTypeInput(categoryId) {
                    this.typeInputs[categoryId] = true;
                    this.render();
                    setTimeout(() => {
                        document.getElementById(`child-type-${categoryId}`)?.focus();
                        this.attachInputKeyHandlers(categoryId, 'type');
                    }, 50);
                },

                hideTypeInput(categoryId) {
                    this.typeInputs[categoryId] = false;
                    this.render();
                },

                attachInputKeyHandlers(categoryId, inputType) {
                    const input = document.getElementById(`child-${inputType}-${categoryId}`);
                    if (!input) return;

                    input.addEventListener('keydown', (e) => {
                        if (e.key === 'Enter') {
                            if (inputType === 'category') {
                                this.createCategory(categoryId);
                            } else {
                                this.createType(categoryId);
                            }
                        } else if (e.key === 'Escape') {
                            if (inputType === 'category') {
                                this.hideCategoryInput(categoryId);
                            } else {
                                this.hideTypeInput(categoryId);
                            }
                        }
                    });
                },

                createCategory(parentId) {
                    let name;

                    if (parentId) {
                        const parentElement = document.querySelector(`[data-category-id="${parentId}"]`);
                        if (parentElement && this.getCategoryLevel(parentElement) >= 1) {
                            return;
                        }

                        const input = document.getElementById(`child-category-${parentId}`);
                        name = input?.value;
                    } else {
                        const input = document.getElementById('new-category-name');
                        name = input?.value;
                    }

                    if (!name?.trim()) return;

                    // Create new category with temporary ID
                    const newCategory = {
                        id: `temp_${this.nextTempId++}`,
                        name: name.trim(),
                        type: 'category',
                        sort: 0,
                        parent_id: parentId,
                        children: [],
                        types: []
                    };

                    // Add to the appropriate location in tree data
                    if (parentId) {
                        const parentCategory = this.findCategoryById(parentId);
                        if (parentCategory) {
                            if (!parentCategory.children) {
                                parentCategory.children = [];
                            }
                            parentCategory.children.push(newCategory);
                        }
                    } else {
                        if (!this.treeData.categories) {
                            this.treeData.categories = [];
                        }
                        this.treeData.categories.push(newCategory);
                    }

                    // Mark as changed and re-render
                    this.markAsChanged();

                    // Hide input and clear form
                    if (parentId) {
                        this.hideCategoryInput(parentId);
                    } else {
                        document.getElementById('show-category-btn').style.display = 'block';
                        document.getElementById('category-input-form').style.display = 'none';
                        document.getElementById('new-category-name').value = '';
                    }

                    this.render();
                },

                createType(categoryId) {
                    let name;

                    if (categoryId) {
                        const input = document.getElementById(`child-type-${categoryId}`);
                        name = input?.value;
                    } else {
                        const input = document.getElementById('new-type-name');
                        name = input?.value;
                    }

                    if (!name?.trim()) return;

                    // Create new type with temporary ID
                    const newType = {
                        id: `temp_${this.nextTempId++}`,
                        name: name.trim(),
                        type: 'type',
                        sort: 0,
                        category_id: categoryId,
                        service_requests_count: 0,
                    };

                    // Add to the appropriate location in tree data
                    if (categoryId) {
                        const category = this.findCategoryById(categoryId);
                        if (category) {
                            if (!category.types) {
                                category.types = [];
                            }
                            category.types.push(newType);
                        }
                    } else {
                        if (!this.treeData.uncategorized_types) {
                            this.treeData.uncategorized_types = [];
                        }
                        this.treeData.uncategorized_types.push(newType);
                    }

                    // Mark as changed and re-render
                    this.markAsChanged();

                    // Hide input and clear form
                    if (categoryId) {
                        this.hideTypeInput(categoryId);
                    } else {
                        document.getElementById('show-type-btn').style.display = 'block';
                        document.getElementById('type-input-form').style.display = 'none';
                        document.getElementById('new-type-name').value = '';
                    }

                    this.render();
                },

                stageCategoryDeletion(categoryId) {
                    this.deletedCategories = this.deletedCategories || [];
                    if (!this.deletedCategories.includes(categoryId)) {
                        this.deletedCategories.push(categoryId);
                    }

                    this.removeCategoryFromTree(categoryId);
                    this.markAsChanged();
                    this.render();
                },

                removeCategoryFromTree(categoryId) {
                    const removedCategory = this.findAndRemoveCategory(categoryId);
                    if (!removedCategory) {
                        return;
                    }

                    const queue = [...(removedCategory.children || [])];

                    while (queue.length) {
                        const child = queue.shift();
                        this.deletedCategories = this.deletedCategories || [];
                        if (!this.deletedCategories.includes(child.id)) {
                            this.deletedCategories.push(child.id);
                        }

                        queue.push(...(child.children || []));

                        (child.types || []).forEach((type) => {
                            this.deletedTypes = this.deletedTypes || [];
                            if (!this.deletedTypes.includes(type.id)) {
                                this.deletedTypes.push(type.id);
                            }
                        });
                    }

                    (removedCategory.types || []).forEach((type) => {
                        this.deletedTypes = this.deletedTypes || [];
                        if (!this.deletedTypes.includes(type.id)) {
                            this.deletedTypes.push(type.id);
                        }
                    });
                },

                confirmDeleteCategory(categoryId) {
                    this.stageCategoryDeletion(categoryId);
                },

                canDeleteCategory(category) {
                    if (!category) {
                        return false;
                    }

                    if ((category.descendant_service_requests_count ?? 0) > 0) {
                        return false;
                    }

                    const typeHasRequests = (category.types || []).some(
                        (type) => (type.service_requests_count ?? 0) > 0,
                    );
                    if (typeHasRequests) {
                        return false;
                    }

                    return (category.children || []).every((child) => this.canDeleteCategory(child));
                },

                removeTypeFromTree(typeId) {
                    this.findAndRemoveType(typeId);
                },

                stageTypeDeletion(typeId) {
                    this.deletedTypes = this.deletedTypes || [];
                    if (!this.deletedTypes.includes(typeId)) {
                        this.deletedTypes.push(typeId);
                    }

                    this.removeTypeFromTree(typeId);
                    this.markAsChanged();
                    this.render();
                },

                startTypeRename(typeId) {
                    this.renamingTypes[typeId] = true;
                    this.render();
                    setTimeout(() => {
                        const input = document.getElementById(`rename-type-${typeId}`);
                        const confirmBtn = document.getElementById(`confirm-rename-type-${typeId}`);

                        if (input) {
                            input.focus();
                            input.select();

                            // Add input event listener for real-time validation
                            input.addEventListener('input', (e) => {
                                if (confirmBtn) {
                                    confirmBtn.disabled = !e.target.value.trim();
                                }
                            });

                            // Add keydown listener for Enter and Escape
                            input.addEventListener('keydown', (e) => {
                                if (e.key === 'Enter' && input.value.trim()) {
                                    this.confirmTypeRename(typeId);
                                } else if (e.key === 'Escape') {
                                    this.cancelTypeRename(typeId);
                                }
                            });
                        }
                    }, 50);
                },

                confirmTypeRename(typeId) {
                    const input = document.getElementById(`rename-type-${typeId}`);
                    const newName = input?.value.trim();

                    if (!newName) {
                        return;
                    }

                    const type = this.findTypeById(typeId);
                    if (type && type.name !== newName) {
                        type.name = newName;
                        this.markAsChanged();
                    }

                    this.renamingTypes[typeId] = false;
                    this.render();
                },

                cancelTypeRename(typeId) {
                    this.renamingTypes[typeId] = false;
                    this.render();
                },

                startCategoryRename(categoryId) {
                    this.renamingCategories[categoryId] = true;
                    this.render();
                    setTimeout(() => {
                        const input = document.getElementById(`rename-category-${categoryId}`);
                        const confirmBtn = document.getElementById(`confirm-rename-category-${categoryId}`);

                        if (input) {
                            input.focus();
                            input.select();

                            // Add input event listener for real-time validation
                            input.addEventListener('input', (e) => {
                                if (confirmBtn) {
                                    confirmBtn.disabled = !e.target.value.trim();
                                }
                            });

                            // Add keydown listener for Enter and Escape
                            input.addEventListener('keydown', (e) => {
                                if (e.key === 'Enter' && input.value.trim()) {
                                    this.confirmCategoryRename(categoryId);
                                } else if (e.key === 'Escape') {
                                    this.cancelCategoryRename(categoryId);
                                }
                            });
                        }
                    }, 50);
                },

                confirmCategoryRename(categoryId) {
                    const input = document.getElementById(`rename-category-${categoryId}`);
                    const newName = input?.value.trim();

                    if (!newName) {
                        return;
                    }

                    const category = this.findCategoryById(categoryId);
                    if (category && category.name !== newName) {
                        category.name = newName;
                        this.markAsChanged();
                    }

                    this.renamingCategories[categoryId] = false;
                    this.render();
                },

                cancelCategoryRename(categoryId) {
                    this.renamingCategories[categoryId] = false;
                    this.render();
                },

                escapeHtml(text) {
                    const div = document.createElement('div');
                    div.textContent = text;
                    return div.innerHTML;
                }
            }));
        </script>
    @endscript
</x-filament-panels::page>

