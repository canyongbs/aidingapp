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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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
    <div id="service-request-type-manager" class="space-y-6" wire:ignore x-data="serviceRequestTypeManager">
        {{-- Hierarchical Tree --}}
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <h2 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Service Request Types & Categories</h2>

            <div class="space-y-2">
                {{-- Uncategorized Types (at top) --}}
                <div id="uncategorized-types" data-sortable="types" data-category-id="" class="space-y-1 min-h-4 p-0.5 rounded transition-colors duration-150 ease-in-out mb-4"></div>

                {{-- Root Level Categories --}}
                <div id="root-categories" data-sortable="categories" data-parent-id="" class="min-h-5 p-0.5 rounded-md transition-colors duration-150 ease-in-out flex flex-col gap-2"></div>
            </div>
        </div>

        {{-- Bottom Action Buttons --}}
        <div class="flex gap-4">
            {{-- Add Category Button --}}
            <div class="flex-1">
                <button
                    id="show-category-btn"
                    type="button"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
                >
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Add Category
                </button>
                <div id="category-input-form" class="flex gap-2" style="display: none;">
                    <input
                        id="new-category-name"
                        type="text"
                        placeholder="Category name..."
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                    />
                    <button
                        id="create-category-btn"
                        type="button"
                        class="inline-flex items-center rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500"
                    >
                        Create
                    </button>
                    <button
                        id="cancel-category-btn"
                        type="button"
                        class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
                    >
                        Cancel
                    </button>
                </div>
            </div>

            {{-- Add Type Button --}}
            <div class="flex-1">
                <button
                    id="show-type-btn"
                    type="button"
                    class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
                >
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Add Type
                </button>
                <div id="type-input-form" class="flex gap-2" style="display: none;">
                    <input
                        id="new-type-name"
                        type="text"
                        placeholder="Type name..."
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                    />
                    <button
                        id="create-type-btn"
                        type="button"
                        class="inline-flex items-center rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500"
                    >
                        Create
                    </button>
                    <button
                        id="cancel-type-btn"
                        type="button"
                        class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
                    >
                        Cancel
                    </button>
                </div>
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
            treeData: @json($this->hierarchicalData),
            categoryInputs: {},
            typeInputs: {},
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
                this.render();
                this.attachEventListeners();
                this.setupDebouncedHandlers();
                this.setupDragAndDrop();

                // Watch for Livewire updates
                $wire.$watch('hierarchicalData', (value) => {
                    this.treeData = value;
                    this.render();
                    this.setupDragAndDrop();
                });
            },

            render() {
                this.renderCategories();
                this.renderUncategorizedTypes();

                // Setup drag and drop after all DOM elements are rendered
                this.setupDragAndDrop();
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
                return `<div data-type-id="${type.id}" class="type-item draggable flex items-center gap-2 rounded-lg border border-gray-200 bg-white p-2 dark:border-gray-600 dark:bg-gray-800 transition-all duration-150 ease-out cursor-grab active:cursor-grabbing" draggable="true">
                        <svg class="type-handle h-4 w-4 cursor-grab text-gray-400 opacity-60 transition-all duration-150 ease-in-out hover:opacity-100 hover:text-primary-500 hover:scale-110 active:cursor-grabbing" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                        <svg class="h-4 w-4 text-primary-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                        <span class="flex-1 text-sm text-gray-700 dark:text-gray-300">${this.escapeHtml(type.name)}</span>
                    </div>`;
            },

            renderCategoryRecursive(category, level) {
                const indent = level * 24;
                const showCategoryInput = this.categoryInputs[category.id] || false;
                const showTypeInput = this.typeInputs[category.id] || false;

                return `<div class="category-wrapper" data-category-id="${category.id}">
                        <div class="category-item draggable flex items-center gap-2 rounded-lg border border-gray-200 bg-gray-50 p-3 dark:border-gray-600 dark:bg-gray-700 transition-all duration-150 ease-out hover:bg-gray-50 hover:-translate-y-px hover:shadow-lg dark:hover:bg-gray-600 cursor-grab active:cursor-grabbing" style="margin-left: ${indent}px" draggable="true" data-category-id="${category.id}">
                            <svg class="category-handle h-5 w-5 cursor-grab text-gray-400 opacity-60 transition-all duration-150 ease-in-out hover:opacity-100 hover:text-primary-500 hover:scale-110 active:cursor-grabbing" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                            <svg class="h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" />
                            </svg>
                            <span class="flex-1 font-medium text-gray-900 dark:text-white">${this.escapeHtml(category.name)}</span>
                            <button @click="showCategoryInput('${category.id}')" class="rounded p-1 text-gray-500 hover:bg-gray-200 hover:text-gray-700 dark:hover:bg-gray-600 dark:hover:text-gray-300" title="Add child category">
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 10.5v6m3-3H9m4.06-7.19l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" />
                                </svg>
                            </button>
                            <button @click="showTypeInput('${category.id}')" class="rounded p-1 text-gray-500 hover:bg-gray-200 hover:text-gray-700 dark:hover:bg-gray-600 dark:hover:text-gray-300" title="Add type">
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                </svg>
                            </button>
                        </div>

                        ${showCategoryInput ? `
                            <div id="category-input-${category.id}" class="flex gap-2 mt-2" style="margin-left: ${indent + 24}px">
                                <input id="child-category-${category.id}" type="text" placeholder="Child category name..." class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white" />
                                <button @click="createCategory('${category.id}')" class="rounded-lg bg-primary-600 px-3 py-1 text-sm text-white hover:bg-primary-700">Create</button>
                                <button @click="hideCategoryInput('${category.id}')" class="rounded-lg border border-gray-300 bg-white px-3 py-1 text-sm text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">Cancel</button>
                            </div>
                        ` : ''}

                        ${showTypeInput ? `
                            <div id="type-input-${category.id}" class="flex gap-2 mt-2" style="margin-left: ${indent + 24}px">
                                <input id="child-type-${category.id}" type="text" placeholder="Type name..." class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white" />
                                <button @click="createType('${category.id}')" class="rounded-lg bg-primary-600 px-3 py-1 text-sm text-white hover:bg-primary-700">Create</button>
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
            },

            setupDragAndDrop() {
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
                    this.dragData.draggedElement.classList.remove('opacity-50', 'rotate-1', 'scale-105', 'z-50', 'shadow-2xl', 'updating-order', 'opacity-70');

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
                    this.dragData.draggedElement.classList.remove('opacity-50', 'rotate-1', 'scale-105', 'z-50', 'shadow-2xl', 'updating-order', 'opacity-70');

                }

                // Also clean up any elements with the dragged ID (in case DOM was manipulated)
                if (this.dragData.draggedType === 'category' && this.dragData.draggedId) {
                    const categoryElement = document.querySelector(`[data-category-id="${this.dragData.draggedId}"]`);
                    if (categoryElement) {
                        categoryElement.classList.remove('opacity-50', 'rotate-1', 'scale-105', 'z-50', 'shadow-2xl', 'updating-order', 'opacity-70');

                        // Also clean wrapper classes
                        const wrapper = categoryElement.closest('.category-wrapper');
                        if (wrapper) {
                            wrapper.classList.remove('ring-2', 'ring-primary-300', 'ring-opacity-50');
                        }
                    }
                } else if (this.dragData.draggedType === 'type' && this.dragData.draggedId) {
                    const typeElement = document.querySelector(`[data-type-id="${this.dragData.draggedId}"]`);
                    if (typeElement) {
                        typeElement.classList.remove('opacity-50', 'rotate-1', 'scale-105', 'z-50', 'shadow-2xl', 'updating-order', 'opacity-70');
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
                        dropPosition.target.classList.add('nest-target', 'bg-primary-500/10', 'border-primary-500/50', 'border-2', 'rounded-lg', 'relative');
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
                    (child.classList.contains('category-wrapper') || child.classList.contains('category-item') || child.classList.contains('type-item'))
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
                    yPosition = prevChildRect.bottom - containerRect.top + ((nextChildRect.top - prevChildRect.bottom) / 2);
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
                    (child.classList.contains('category-wrapper') || child.classList.contains('category-item') || child.classList.contains('type-item'))
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
                if (target.classList.contains('category-item') && this.dragData.draggedType === 'category') {
                    const draggedLevel = this.getCategoryLevel(this.dragData.draggedElement);
                    const targetLevel = this.getCategoryLevel(target);

                    // Check if they share the same parent context
                    const draggedParentContext = this.getCategoryParentContext(this.dragData.draggedElement);
                    const targetParentContext = this.getCategoryParentContext(target);



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
                        // Find the container for insertion positioning
                        const targetWrapper = target.closest('.category-wrapper');
                        const container = targetWrapper.parentElement;

                        // Check if we're hovering over the center for nesting
                        const rect = target.getBoundingClientRect();
                        const y = e.clientY - rect.top;
                        const height = rect.height;

                        if (y > height * 0.3 && y < height * 0.7) {
                            return { type: 'inside', target: target };
                        } else {
                            // Calculate insertion position based on mouse Y
                            const insertIndex = this.calculateInsertionPosition(container, e.clientY);
                            return {
                                type: 'insert',
                                container: container,
                                insertIndex: insertIndex
                            };
                        }
                    } else {
                        // Different contexts - only allow nesting
                        return { type: 'inside', target: target };
                    }
                }

                if (target.classList.contains('type-item') && this.dragData.draggedType === 'type') {
                    // For types, check if they're in the same category container
                    const draggedContainer = this.dragData.draggedElement.closest('[data-sortable="types"]');
                    const targetContainer = target.closest('[data-sortable="types"]');

                    if (draggedContainer && targetContainer &&
                        draggedContainer.dataset.categoryId === targetContainer.dataset.categoryId) {
                        // Same category container - allow insertion
                        const insertIndex = this.calculateInsertionPosition(targetContainer, e.clientY);
                        return {
                            type: 'insert',
                            container: targetContainer,
                            insertIndex: insertIndex
                        };
                    } else {
                        // Different categories - don't allow type-to-type drops across categories
                        return null;
                    }
                }

                // For category-to-type or type-to-category interactions, only allow nesting types into categories
                if (target.classList.contains('category-item') && this.dragData.draggedType === 'type') {
                    return { type: 'inside', target: target };
                }

                return null; // Invalid drop
            },

            cleanupDropIndicators() {
                this.cleanupInsertionLines();
                document.querySelectorAll('.nest-target').forEach(target => {
                    target.classList.remove('nest-target', 'bg-primary-500/10', 'border-primary-500/50', 'border-2', 'rounded-lg', 'relative');
                });
            },

            cleanupDragVisuals() {
                this.cleanupDropIndicators();
                document.removeEventListener('dragover', this.updateGhostPosition);
            },

            performDrop(target, position) {
                // Add updating state to the dragged element
                const draggedElement = this.dragData.draggedElement;
                if (draggedElement) {
                    draggedElement.classList.add('updating-order', 'opacity-70', 'relative');

                }

                // Determine the drop operation based on dragged type and target
                if (this.dragData.draggedType === 'category') {
                    this.handleCategoryDrop(target, position);
                } else if (this.dragData.draggedType === 'type') {
                    this.handleTypeDrop(target, position);
                }
            },

            handleCategoryDrop(target, position) {
                const categoryId = this.dragData.draggedId;
                let newParentId = null;
                let insertIndex = null;

                if (position.type === 'inside') {
                    // Nesting inside another category
                    newParentId = position.target.dataset.categoryId;
                } else if (position.type === 'insert') {
                    // Inserting at specific position
                    insertIndex = position.insertIndex;
                    // Determine parent from container's data-parent-id
                    newParentId = position.container.dataset.parentId || null;

                    // Handle empty string as null for root level
                    if (newParentId === '') {
                        newParentId = null;
                    }
                }

                // Update immediately to move the element in DOM
                this.updateCategoryPositionInDOM(categoryId, newParentId, position);

                // Reattach event listeners to moved elements
                this.setupDragAndDrop();

                // Then send the update to backend
                this.debouncedUpdateCategoriesOrder({
                    categoryId,
                    newParentId,
                    insertIndex
                });
            },

            handleTypeDrop(target, position) {
                const typeId = this.dragData.draggedId;
                let newCategoryId = null;
                let insertIndex = null;

                if (position.type === 'inside') {
                    // Moving type to a category
                    newCategoryId = position.target.dataset.categoryId;
                } else if (position.type === 'insert') {
                    // Inserting at specific position within same category
                    insertIndex = position.insertIndex;
                    newCategoryId = position.container.dataset.categoryId || null;
                }

                // Update immediately to move the element in DOM
                this.updateTypePositionInDOM(typeId, newCategoryId, position);

                // Reattach event listeners to moved elements
                this.setupDragAndDrop();

                // Then send the update to backend
                this.debouncedUpdateTypesOrder({
                    typeId,
                    newCategoryId,
                    insertIndex
                });
            },

            findCategoryParent(categoryId) {
                // Search through the tree to find the parent of a category
                const findParent = (categories, targetId, parentId = null) => {
                    for (const category of categories) {
                        if (category.id == targetId) {
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
                // Search through the tree to find which category a type belongs to
                const findCategory = (categories) => {
                    for (const category of categories) {
                        if (category.types && category.types.some(type => type.id == typeId)) {
                            return category.id;
                        }
                        if (category.children && category.children.length > 0) {
                            const result = findCategory(category.children);
                            if (result) return result;
                        }
                    }
                    return null;
                };

                // Check categories first
                const categoryResult = findCategory(this.treeData.categories || []);
                if (categoryResult) return categoryResult;

                // Check uncategorized types
                if (this.treeData.uncategorized_types &&
                    this.treeData.uncategorized_types.some(type => type.id == typeId)) {
                    return null;
                }

                return null;
            },

            calculateInsertIndex(targetId, position, type) {
                // Calculate where to insert the item based on target and position
                // This would need to be implemented based on your specific requirements
                // For now, return null to append at the end
                return null;
            },

            updateCategoryPositionInDOM(categoryId, newParentId, position) {
                // Find the complete category wrapper that includes all children
                const draggedWrapper = document.querySelector(`[data-category-id="${categoryId}"]`).closest('.category-wrapper');
                if (!draggedWrapper) {
                    return;
                }

                // Calculate the new nesting level based on position type
                let newLevel = 0;
                if (position.type === 'inside') {
                    // Nesting inside - calculate target's level + 1
                    newLevel = this.getCategoryLevel(position.target) + 1;
                } else if (position.type === 'insert') {
                    // Inserting at container level - determine level from parent container
                    const parentContainer = position.container.closest('[data-sortable="categories"]');
                    if (parentContainer && parentContainer.dataset.parentId) {
                        // Find a category in this container to get the level
                        const siblingCategory = position.container.querySelector('.category-item');
                        if (siblingCategory) {
                            newLevel = this.getCategoryLevel(siblingCategory);
                        }
                    } else {
                        // Root level
                        newLevel = 0;
                    }
                }

                // Update the indentation of the dragged element and all its children
                this.updateCategoryIndentation(draggedWrapper, newLevel);

                // Remove the entire wrapper from current position (this includes all children)
                draggedWrapper.remove();

                // Find target container and insert based on position type
                if (position.type === 'inside') {
                    // Insert as child of target category - always append to the end
                    const targetWrapper = position.target.closest('.category-wrapper');
                    let childContainer = targetWrapper.querySelector(`[data-parent-id="${position.target.dataset.categoryId}"]`);

                    if (!childContainer) {
                        // Create the child container if it doesn't exist
                        childContainer = document.createElement('div');
                        childContainer.setAttribute('data-sortable', 'categories');
                        childContainer.setAttribute('data-parent-id', position.target.dataset.categoryId);
                        childContainer.className = 'mt-2 space-y-2 min-h-5 p-0.5 rounded-md transition-colors duration-150 ease-in-out';

                        // Insert the container after types but before any existing child categories
                        const existingChildContainer = targetWrapper.querySelector(`[data-parent-id="${position.target.dataset.categoryId}"]`);
                        const typesContainer = targetWrapper.querySelector(`[data-sortable="types"]`);

                        if (existingChildContainer) {
                            // If there's already a child container, we shouldn't be here, but append anyway
                            targetWrapper.appendChild(childContainer);
                        } else if (typesContainer) {
                            // Insert after the types container
                            typesContainer.insertAdjacentElement('afterend', childContainer);
                        } else {
                            // Just append to the category wrapper
                            targetWrapper.appendChild(childContainer);
                        }
                    }

                    // Always append to the end of existing children (entire wrapper with all children)
                    childContainer.appendChild(draggedWrapper);
                } else if (position.type === 'insert') {
                    // Insert at specific index in container
                    const children = Array.from(position.container.children).filter(child =>
                        child.classList.contains('category-wrapper')
                    );

                    if (position.insertIndex >= children.length) {
                        // Insert at end
                        position.container.appendChild(draggedWrapper);
                    } else {
                        // Insert at specific position
                        position.container.insertBefore(draggedWrapper, children[position.insertIndex]);
                    }
                }


            },

            updateTypePositionInDOM(typeId, newCategoryId, position) {
                const draggedElement = document.querySelector(`[data-type-id="${typeId}"]`);
                if (!draggedElement) {
                    return;
                }

                // Remove from current position
                draggedElement.remove();

                // Find or create target container based on position type
                let targetContainer;

                if (position.type === 'inside') {
                    // Moving to a category
                    const categoryWrapper = position.target.closest('.category-wrapper');
                    targetContainer = categoryWrapper.querySelector(`[data-sortable="types"][data-category-id="${position.target.dataset.categoryId}"]`);

                    if (!targetContainer) {
                        // Create the types container if it doesn't exist
                        targetContainer = document.createElement('div');
                        targetContainer.setAttribute('data-sortable', 'types');
                        targetContainer.setAttribute('data-category-id', position.target.dataset.categoryId);
                        targetContainer.className = 'mt-2 space-y-1 min-h-4 p-0.5 rounded transition-colors duration-150 ease-in-out';

                        // Calculate the indentation for the types container
                        const categoryLevel = this.getCategoryLevel(position.target);
                        const indent = (categoryLevel + 1) * 24;
                        targetContainer.style.marginLeft = `${indent}px`;

                        // Find the right place to insert it (after the category item but before child categories)
                        const childCategories = categoryWrapper.querySelector(`[data-parent-id="${position.target.dataset.categoryId}"]`);
                        if (childCategories) {
                            categoryWrapper.insertBefore(targetContainer, childCategories);
                        } else {
                            categoryWrapper.appendChild(targetContainer);
                        }
                    }

                    // Always append to the end when moving to a category
                    targetContainer.appendChild(draggedElement);
                } else if (position.type === 'insert') {
                    // Moving within same container at specific position
                    targetContainer = position.container;

                    const children = Array.from(targetContainer.children).filter(child =>
                        child.classList.contains('type-item')
                    );

                    if (position.insertIndex >= children.length) {
                        // Insert at end
                        targetContainer.appendChild(draggedElement);
                    } else {
                        // Insert at specific position
                        targetContainer.insertBefore(draggedElement, children[position.insertIndex]);
                    }
                }


            },

            getCategoryLevel(categoryElement) {
                // Calculate the nesting level of a category by counting parent containers
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
                // Get the parent container ID for a category element
                const wrapper = categoryElement.closest('.category-wrapper');
                if (!wrapper) return null;

                // Find the immediate parent sortable container
                const parentContainer = wrapper.parentElement.closest('[data-sortable="categories"]');

                if (!parentContainer) {
                    // This shouldn't happen, but treat as root
                    return 'root';
                }

                // If the parent container has a parentId, use it; otherwise it's root level
                const parentId = parentContainer.dataset.parentId;
                if (!parentId || parentId === '' || parentId === 'null') {
                    return 'root';
                }

                return parentId;
            },

            updateCategoryIndentation(categoryWrapper, level) {
                // Update indentation for the main category element
                const categoryItem = categoryWrapper.querySelector(':scope > .category-item');
                if (categoryItem) {
                    const indent = level * 24;
                    categoryItem.style.marginLeft = `${indent}px`;
                }

                // Update indentation for direct type containers in this category
                const directTypeContainers = categoryWrapper.querySelectorAll(':scope > [data-sortable="types"]');
                directTypeContainers.forEach(container => {
                    const indent = (level + 1) * 24;
                    container.style.marginLeft = `${indent}px`;
                });

                // Recursively update all child categories
                const childContainers = categoryWrapper.querySelectorAll(':scope > [data-sortable="categories"]');
                childContainers.forEach(childContainer => {
                    const childWrappers = childContainer.querySelectorAll(':scope > .category-wrapper');
                    childWrappers.forEach(childWrapper => {
                        this.updateCategoryIndentation(childWrapper, level + 1);
                    });
                });
            },

            debouncedUpdateCategoriesOrder: null,
            debouncedUpdateTypesOrder: null,

            setupDebouncedHandlers() {
                // Debounce category updates
                this.debouncedUpdateCategoriesOrder = this.debounce((dropData) => {
                    const { categoryId, newParentId, insertIndex } = dropData;

                    // Get the current order of categories in the target container
                    const targetContainer = newParentId
                        ? document.querySelector(`[data-sortable="categories"][data-parent-id="${newParentId}"]`)
                        : document.querySelector('[data-sortable="categories"][data-parent-id=""]');

                    let orderedIds = [];
                    if (targetContainer) {
                        orderedIds = Array.from(targetContainer.children)
                            .map(child => child.dataset.categoryId)
                            .filter(Boolean);
                    }

                    $wire.updateCategoriesOrder(orderedIds, newParentId)
                        .finally(() => {
                            const element = document.querySelector(`[data-category-id="${categoryId}"]`);
                            if (element) {
                                // Only remove updating classes, preserve normal opacity
                                element.classList.remove('updating-order', 'opacity-70', 'relative');
                                // Ensure no drag classes remain
                                element.classList.remove('opacity-50', 'rotate-1', 'scale-105', 'z-50', 'shadow-2xl');
                            }
                        });
                }, 250);

                // Debounce type updates
                this.debouncedUpdateTypesOrder = this.debounce((dropData) => {
                    const { typeId, newCategoryId, insertIndex } = dropData;

                    // Get the current order of types in the target container
                    const targetContainer = newCategoryId
                        ? document.querySelector(`[data-sortable="types"][data-category-id="${newCategoryId}"]`)
                        : document.querySelector('[data-sortable="types"][data-category-id=""]');

                    let orderedIds = [];
                    if (targetContainer) {
                        orderedIds = Array.from(targetContainer.children)
                            .map(child => child.dataset.typeId)
                            .filter(Boolean);
                    }

                    $wire.updateTypesOrder(orderedIds, newCategoryId)
                        .finally(() => {
                            const element = document.querySelector(`[data-type-id="${typeId}"]`);
                            if (element) {
                                // Only remove updating classes, preserve normal opacity
                                element.classList.remove('updating-order', 'opacity-70', 'relative');
                                // Ensure no drag classes remain
                                element.classList.remove('opacity-50', 'rotate-1', 'scale-105', 'z-50', 'shadow-2xl');
                            }
                        });
                }, 250);
            },

            // Debounce utility function
            debounce(func, wait) {
                let timeout;
                return function executedFunction(...args) {
                    const later = () => {
                        clearTimeout(timeout);
                        func(...args);
                    };
                    clearTimeout(timeout);
                    timeout = setTimeout(later, wait);
                };
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

            async createCategory(parentId) {
                let name;

                if (parentId) {
                    const input = document.getElementById(`child-category-${parentId}`);
                    name = input?.value;
                } else {
                    const input = document.getElementById('new-category-name');
                    name = input?.value;
                }

                if (!name?.trim()) return;

                await $wire.createCategory(parentId, name.trim());

                if (parentId) {
                    this.hideCategoryInput(parentId);
                } else {
                    document.getElementById('show-category-btn').style.display = 'block';
                    document.getElementById('category-input-form').style.display = 'none';
                    document.getElementById('new-category-name').value = '';
                }
            },

            async createType(categoryId) {
                let name;

                if (categoryId) {
                    const input = document.getElementById(`child-type-${categoryId}`);
                    name = input?.value;
                } else {
                    const input = document.getElementById('new-type-name');
                    name = input?.value;
                }

                if (!name?.trim()) return;

                await $wire.createType(categoryId, name.trim());

                if (categoryId) {
                    this.hideTypeInput(categoryId);
                } else {
                    document.getElementById('show-type-btn').style.display = 'block';
                    document.getElementById('type-input-form').style.display = 'none';
                    document.getElementById('new-type-name').value = '';
                }
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

