/*
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
*/
document.addEventListener('alpine:init', () => {
    Alpine.data('serviceRequestTypeManager', ({ originalTreeData, treeData, canEdit }) => ({
        originalTreeData,
        treeData,
        canEdit,
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

            // Only setup drag and drop if we're not currently dragging
            // to avoid attaching duplicate event listeners during a drag operation
            if (!this.dragData.isDragging) {
                this.setupDragAndDrop();
            }
        },

        updateSaveButton() {
            // Buttons are now reactive using x-bind:disabled in Alpine.js
            // No need to manually update DOM
        },

        renderCategories() {
            const container = document.getElementById('root-categories');
            if (!container) return;

            container.innerHTML = '';

            if (this.treeData.categories && this.treeData.categories.length > 0) {
                this.treeData.categories.forEach((category) => {
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
                this.treeData.uncategorized_types.forEach((type) => {
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
            const requestCount = typeof type.service_requests_count === 'number' ? type.service_requests_count : 0;
            const canDelete = requestCount === 0;
            const isRenaming = this.renamingTypes[type.id] || false;

            return `<div data-type-id="${type.id}" class="type-item ${this.canEdit ? 'draggable cursor-grab active:cursor-grabbing' : ''} flex items-center gap-2 rounded-lg border border-gray-200 bg-white p-2 dark:border-gray-600 dark:bg-gray-800 transition-all duration-150 ease-out" ${this.canEdit ? 'draggable="true"' : ''}>
                        ${
                            this.canEdit
                                ? `<div class="p-1 -m-1 cursor-grab">
                                            <svg class="type-handle h-4 w-4 text-gray-400 opacity-60 transition-all duration-150 ease-in-out hover:opacity-100 hover:text-primary-500 hover:scale-110 active:cursor-grabbing" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                                            </svg>
                                        </div>`
                                : ''
                        }
                        ${
                            this.canEdit && isRenaming
                                ? `
                                    <input
                                        id="rename-type-${type.id}"
                                        type="text"
                                        value="${this.escapeHtml(type.name)}"
                                        class="flex-1 h-5 text-sm text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-primary-500 rounded px-2 py-0 focus:outline-none focus:ring-2 focus:ring-primary-500"
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
                                                `
                                : `
                                    <a
                                        href="${type.view_url}"
                                        target="${this.hasUnsavedChanges ? '_blank' : '_self'}"
                                        class="flex-1 text-sm text-gray-700 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 cursor-pointer transition-colors"
                                    >${this.escapeHtml(type.name)}</a>
                            `
                        }
                        <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-semibold text-gray-600 dark:bg-gray-600 dark:text-gray-100" x-tooltip.raw="Number of service requests">${requestCount}</span>
                        ${
                            this.canEdit && !isRenaming
                                ? `<button type="button" class="p-1.5 -m-1 rounded hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors" @click.stop="startTypeRename('${type.id}')" x-tooltip.raw="Rename">
                                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor">
                                        <path d="M13.488 2.513a1.75 1.75 0 0 0-2.475 0L6.75 6.774a2.75 2.75 0 0 0-.596.892l-.848 2.047a.75.75 0 0 0 .98.98l2.047-.848a2.75 2.75 0 0 0 .892-.596l4.261-4.262a1.75 1.75 0 0 0 0-2.474Z" />
                                        <path d="M4.75 3.5c-.69 0-1.25.56-1.25 1.25v6.5c0 .69.56 1.25 1.25 1.25h6.5c.69 0 1.25-.56 1.25-1.25V9A.75.75 0 0 1 14 9v2.25A2.75 2.75 0 0 1 11.25 14h-6.5A2.75 2.75 0 0 1 2 11.25v-6.5A2.75 2.75 0 0 1 4.75 2H7a.75.75 0 0 1 0 1.5H4.75Z" />
                                    </svg>
                                </button>`
                                : ''
                        }
                        ${
                            this.canEdit && canDelete && !isRenaming
                                ? `<button type="button" class="p-1.5 -m-1 rounded hover:bg-red-50 dark:hover:bg-red-900/20 text-red-600 hover:text-red-800 transition-colors" @click.stop="stageTypeDeletion('${type.id}')" x-tooltip.raw="Delete">
                                        <svg class="size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5 3.25V4H2.75a.75.75 0 0 0 0 1.5h.3l.815 8.15A1.5 1.5 0 0 0 5.357 15h5.285a1.5 1.5 0 0 0 1.493-1.35l.815-8.15h.3a.75.75 0 0 0 0-1.5H11v-.75A2.25 2.25 0 0 0 8.75 1h-1.5A2.25 2.25 0 0 0 5 3.25Zm2.25-.75a.75.75 0 0 0-.75.75V4h3v-.75a.75.75 0 0 0-.75-.75h-1.5ZM6.05 6a.75.75 0 0 1 .787.713l.275 5.5a.75.75 0 0 1-1.498.075l-.275-5.5A.75.75 0 0 1 6.05 6Zm3.9 0a.75.75 0 0 1 .712.787l-.275 5.5a.75.75 0 0 1-1.498-.075l.275-5.5a.75.75 0 0 1 .786-.711Z" clip-rule="evenodd" />
                                        </svg>
                                    </button>`
                                : ''
                        }
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
                            ${
                                this.canEdit
                                    ? `<div class="p-1 -m-1 cursor-grab">
                                            <svg class="category-handle size-5 text-gray-400 opacity-60 transition-all duration-150 ease-in-out hover:opacity-100 hover:text-primary-500 hover:scale-110 active:cursor-grabbing" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M2 4.75A.75.75 0 0 1 2.75 4h14.5a.75.75 0 0 1 0 1.5H2.75A.75.75 0 0 1 2 4.75ZM2 10a.75.75 0 0 1 .75-.75h14.5a.75.75 0 0 1 0 1.5H2.75A.75.75 0 0 1 2 10Zm0 5.25a.75.75 0 0 1 .75-.75h14.5a.75.75 0 0 1 0 1.5H2.75a.75.75 0 0 1-.75-.75Z" clip-rule="evenodd" />
                                            </svg>
                                        </div>`
                                    : ''
                            }
                            <svg class="h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M3.75 3A1.75 1.75 0 0 0 2 4.75v3.26a3.235 3.235 0 0 1 1.75-.51h12.5c.644 0 1.245.188 1.75.51V6.75A1.75 1.75 0 0 0 16.25 5h-4.836a.25.25 0 0 1-.177-.073L9.823 3.513A1.75 1.75 0 0 0 8.586 3H3.75ZM3.75 9A1.75 1.75 0 0 0 2 10.75v4.5c0 .966.784 1.75 1.75 1.75h12.5A1.75 1.75 0 0 0 18 15.25v-4.5A1.75 1.75 0 0 0 16.25 9H3.75Z" />
                            </svg>
                            ${
                                this.canEdit && isRenaming
                                    ? `
                                        <input
                                            id="rename-category-${category.id}"
                                            type="text"
                                            value="${this.escapeHtml(category.name)}"
                                            class="flex-1 h-6 font-medium text-gray-900 dark:text-white bg-white dark:bg-gray-600 border border-primary-500 rounded px-2 py-0 focus:outline-none focus:ring-2 focus:ring-primary-500"
                                        />
                                        <button
                                            type="button"
                                            class="text-green-600 hover:text-green-800 disabled:opacity-50 disabled:cursor-not-allowed"
                                            @click.stop="confirmCategoryRename('${category.id}')"
                                            id="confirm-rename-category-${category.id}"
                                        >
                                            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor">
                                                <path fill-rule="evenodd" d="M12.416 3.376a.75.75 0 0 1 .208 1.04l-5 7.5a.75.75 0 0 1-1.154.114l-3-3a.75.75 0 0 1 1.06-1.06l2.353 2.353 4.493-6.74a.75.75 0 0 1 1.04-.207Z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    `
                                    : `
                                        <span class="flex-1 font-medium text-gray-900 dark:text-white">${this.escapeHtml(category.name)}</span>
                                `
                            }
                            ${
                                this.canEdit && !isRenaming
                                    ? `<button @click="showTypeInput('${category.id}')" class="p-1.5 -m-1 rounded hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors" x-tooltip.raw="Add type in category">
                                            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor">
                                                <path d="M8.75 3.75a.75.75 0 0 0-1.5 0v3.5h-3.5a.75.75 0 0 0 0 1.5h3.5v3.5a.75.75 0 0 0 1.5 0v-3.5h3.5a.75.75 0 0 0 0-1.5h-3.5v-3.5Z" />
                                            </svg>
                                        </button>`
                                    : ''
                            }
                            ${
                                this.canEdit && canAddChildCategory && !isRenaming
                                    ? `
                                        <button @click="showCategoryInput('${category.id}')" class="p-1.5 -m-1 rounded hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors" x-tooltip.raw="Add child category">
                                            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor">
                                                <path fill-rule="evenodd" d="M3.5 2A1.5 1.5 0 0 0 2 3.5v9A1.5 1.5 0 0 0 3.5 14h9a1.5 1.5 0 0 0 1.5-1.5v-7A1.5 1.5 0 0 0 12.5 4H9.621a1.5 1.5 0 0 1-1.06-.44L7.439 2.44A1.5 1.5 0 0 0 6.38 2H3.5ZM8 6a.75.75 0 0 1 .75.75v1.5h1.5a.75.75 0 0 1 0 1.5h-1.5v1.5a.75.75 0 0 1-1.5 0v-1.5h-1.5a.75.75 0 0 1 0-1.5h1.5v-1.5A.75.75 0 0 1 8 6Z" clip-rule="evenodd" />
                                            </svg>
                                        </button>`
                                    : ''
                            }
                            ${
                                this.canEdit && !isRenaming
                                    ? `<button type="button" class="p-1.5 -m-1 rounded hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors" @click.stop="startCategoryRename('${category.id}')" x-tooltip.raw="Rename">
                                        <svg class="size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor">
                                            <path d="M13.488 2.513a1.75 1.75 0 0 0-2.475 0L6.75 6.774a2.75 2.75 0 0 0-.596.892l-.848 2.047a.75.75 0 0 0 .98.98l2.047-.848a2.75 2.75 0 0 0 .892-.596l4.261-4.262a1.75 1.75 0 0 0 0-2.474Z" />
                                            <path d="M4.75 3.5c-.69 0-1.25.56-1.25 1.25v6.5c0 .69.56 1.25 1.25 1.25h6.5c.69 0 1.25-.56 1.25-1.25V9A.75.75 0 0 1 14 9v2.25A2.75 2.75 0 0 1 11.25 14h-6.5A2.75 2.75 0 0 1 2 11.25v-6.5A2.75 2.75 0 0 1 4.75 2H7a.75.75 0 0 1 0 1.5H4.75Z" />
                                        </svg>
                                    </button>`
                                    : ''
                            }
                            ${
                                this.canEdit && this.canDeleteCategory(category) && !isRenaming
                                    ? `<button type="button" class="p-1.5 -m-1 rounded hover:bg-red-50 dark:hover:bg-red-900/20 text-red-600 hover:text-red-800 transition-colors" @click.stop="confirmDeleteCategory('${category.id}')" x-tooltip.raw="Delete">
                                            <svg class="size-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor">
                                                <path fill-rule="evenodd" d="M5 3.25V4H2.75a.75.75 0 0 0 0 1.5h.3l.815 8.15A1.5 1.5 0 0 0 5.357 15h5.285a1.5 1.5 0 0 0 1.493-1.35l.815-8.15h.3a.75.75 0 0 0 0-1.5H11v-.75A2.25 2.25 0 0 0 8.75 1h-1.5A2.25 2.25 0 0 0 5 3.25Zm2.25-.75a.75.75 0 0 0-.75.75V4h3v-.75a.75.75 0 0 0-.75-.75h-1.5ZM6.05 6a.75.75 0 0 1 .787.713l.275 5.5a.75.75 0 0 1-1.498.075l-.275-5.5A.75.75 0 0 1 6.05 6Zm3.9 0a.75.75 0 0 1 .712.787l-.275 5.5a.75.75 0 0 1-1.498-.075l.275-5.5a.75.75 0 0 1 .786-.711Z" clip-rule="evenodd" />
                                            </svg>
                                        </button>`
                                    : ''
                            }
                        </div>

                        ${
                            this.canEdit && canAddChildCategory && showCategoryInput
                                ? `
                                    <div id="category-input-${category.id}" class="flex gap-2 mt-2" style="margin-left: ${indent + 24}px">
                                        <input id="child-category-${category.id}" type="text" placeholder="New child category name." class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white" />
                                        <button @click="createCategory('${category.id}')" class="rounded-lg bg-primary-600 px-3 py-1 text-sm text-white hover:bg-primary-700">Add</button>
                                        <button @click="hideCategoryInput('${category.id}')" class="rounded-lg border border-gray-300 bg-white px-3 py-1 text-sm text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">Cancel</button>
                                    </div>
                                `
                                : ''
                        }

                        ${
                            this.canEdit && showTypeInput
                                ? `
                                    <div id="type-input-${category.id}" class="flex gap-2 mt-2" style="margin-left: ${indent + 24}px">
                                        <input id="child-type-${category.id}" type="text" placeholder="Type name..." class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white" />
                                        <button @click="createType('${category.id}')" class="rounded-lg bg-primary-600 px-3 py-1 text-sm text-white hover:bg-primary-700">Add</button>
                                        <button @click="hideTypeInput('${category.id}')" class="rounded-lg border border-gray-300 bg-white px-3 py-1 text-sm text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200">Cancel</button>
                                    </div>
                                `
                                : ''
                        }

                        ${
                            category.types && category.types.length > 0
                                ? `
                                    <div data-sortable="types" data-category-id="${category.id}" class="mt-2 space-y-1 min-h-4 p-0.5 rounded transition-colors duration-150 ease-in-out" style="margin-left: ${indent + 24}px">
                                        ${category.types.map((type) => this.renderType(type)).join('')}
                                    </div>
                                `
                                : ''
                        }

                        ${
                            category.children && category.children.length > 0
                                ? `
                                    <div data-sortable="categories" data-parent-id="${category.id}" class="mt-2 space-y-2 min-h-5 p-0.5 rounded-md transition-colors duration-150 ease-in-out">
                                        ${category.children.map((child) => this.renderCategoryRecursive(child, level + 1)).join('')}
                                    </div>
                                `
                                : ''
                        }
                    </div>`;
        },

        attachEventListeners() {
            // Show category input button
            document.getElementById('show-category-btn')?.addEventListener('click', () => {
                // Completely hide the add-type button while adding a category
                const showTypeBtn = document.getElementById('show-type-btn');
                if (showTypeBtn) showTypeBtn.style.display = 'none';
                document.getElementById('type-input-form').style.display = 'none';
                if (document.getElementById('new-type-name')) document.getElementById('new-type-name').value = '';

                // Show category input
                document.getElementById('show-category-btn').style.display = 'none';
                document.getElementById('category-input-form').style.display = 'flex';
                document.getElementById('new-category-name')?.focus();
            });

            // Cancel category button
            document.getElementById('cancel-category-btn')?.addEventListener('click', () => {
                document.getElementById('show-category-btn').style.display = 'block';
                document.getElementById('category-input-form').style.display = 'none';
                document.getElementById('new-category-name').value = '';

                // Reshow the add-type button when category add is cancelled
                const showTypeBtn = document.getElementById('show-type-btn');
                if (showTypeBtn) showTypeBtn.style.display = 'block';
            });

            // Create category button
            document.getElementById('create-category-btn')?.addEventListener('click', () => {
                this.createCategory(null);

                // After confirming create, reshow the add-type button
                const showTypeBtn = document.getElementById('show-type-btn');
                if (showTypeBtn) showTypeBtn.style.display = 'block';
            });

            // Category input enter key
            document.getElementById('new-category-name')?.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    this.createCategory(null);

                    // After confirming create via Enter, reshow add-type
                    const showTypeBtn = document.getElementById('show-type-btn');
                    if (showTypeBtn) showTypeBtn.style.display = 'block';
                } else if (e.key === 'Escape') {
                    document.getElementById('cancel-category-btn')?.click();
                }
            });

            // Show type input button
            document.getElementById('show-type-btn')?.addEventListener('click', () => {
                // Completely hide the add-category button while adding a type
                const showCategoryBtn = document.getElementById('show-category-btn');
                if (showCategoryBtn) showCategoryBtn.style.display = 'none';
                document.getElementById('category-input-form').style.display = 'none';
                if (document.getElementById('new-category-name')) document.getElementById('new-category-name').value = '';

                // Show type input
                document.getElementById('show-type-btn').style.display = 'none';
                document.getElementById('type-input-form').style.display = 'flex';
                document.getElementById('new-type-name')?.focus();
            });

            // Cancel type button
            document.getElementById('cancel-type-btn')?.addEventListener('click', () => {
                document.getElementById('show-type-btn').style.display = 'block';
                document.getElementById('type-input-form').style.display = 'none';
                document.getElementById('new-type-name').value = '';

                // Reshow add-category when type add is cancelled
                const showCategoryBtn = document.getElementById('show-category-btn');
                if (showCategoryBtn) showCategoryBtn.style.display = 'block';
            });

            // Create type button
            document.getElementById('create-type-btn')?.addEventListener('click', () => {
                this.createType(null);

                // After confirming create, reshow add-category
                const showCategoryBtn = document.getElementById('show-category-btn');
                if (showCategoryBtn) showCategoryBtn.style.display = 'block';
            });

            // Type input enter key
            document.getElementById('new-type-name')?.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    this.createType(null);

                    // After confirming create via Enter, reshow add-category
                    const showCategoryBtn = document.getElementById('show-category-btn');
                    if (showCategoryBtn) showCategoryBtn.style.display = 'block';
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
                draggableElements.forEach((el) => {
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
                document.querySelectorAll('.category-item, .type-item').forEach((el) => {
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

                document.querySelectorAll('[data-sortable="types"]').forEach((container) => {
                    if (container._dragOverHandler) {
                        container.removeEventListener('dragover', container._dragOverHandler);
                    }
                    if (container._dropHandler) {
                        container.removeEventListener('drop', container._dropHandler);
                    }
                    if (container._dragEnterHandler) {
                        container.removeEventListener('dragenter', container._dragEnterHandler);
                    }
                    if (container._dragLeaveHandler) {
                        container.removeEventListener('dragleave', container._dragLeaveHandler);
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
                this.dragData.draggedElement.classList.remove(
                    'opacity-50',
                    'rotate-1',
                    'scale-105',
                    'z-50',
                    'shadow-2xl',
                    'updating-order',
                    'opacity-70',
                );

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

            // Re-setup drag and drop handlers after the DOM has been updated
            // Use setTimeout to run after the current event loop completes
            setTimeout(() => {
                this.setupDragAndDrop();
            }, 100);
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
            e.stopPropagation(); // Prevent event from bubbling to parent elements

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
                this.dragData.draggedElement.classList.remove(
                    'opacity-50',
                    'rotate-1',
                    'scale-105',
                    'z-50',
                    'shadow-2xl',
                    'updating-order',
                    'opacity-70',
                );
            }

            // Also clean up any elements with the dragged ID (in case DOM was manipulated)
            if (this.dragData.draggedType === 'category' && this.dragData.draggedId) {
                const categoryElement = document.querySelector(`[data-category-id="${this.dragData.draggedId}"]`);
                if (categoryElement) {
                    categoryElement.classList.remove(
                        'opacity-50',
                        'rotate-1',
                        'scale-105',
                        'z-50',
                        'shadow-2xl',
                        'updating-order',
                        'opacity-70',
                    );

                    // Also clean wrapper classes
                    const wrapper = categoryElement.closest('.category-wrapper');
                    if (wrapper) {
                        wrapper.classList.remove('ring-2', 'ring-primary-300', 'ring-opacity-50');
                    }
                }
            } else if (this.dragData.draggedType === 'type' && this.dragData.draggedId) {
                const typeElement = document.querySelector(`[data-type-id="${this.dragData.draggedId}"]`);
                if (typeElement) {
                    typeElement.classList.remove(
                        'opacity-50',
                        'rotate-1',
                        'scale-105',
                        'z-50',
                        'shadow-2xl',
                        'updating-order',
                        'opacity-70',
                    );
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
                    dropPosition.target.classList.add(
                        'nest-target',
                        'bg-primary-500/10',
                        'border-primary-500/50',
                        'border-2',
                        'rounded-lg',
                        'relative',
                    );
                }
            } else if (dropPosition.type === 'insert') {
                // Show single insertion line at calculated position
                this.showInsertionLine(dropPosition.container, dropPosition.insertIndex);
            }
        },

        showInsertionLine(container, insertIndex) {
            // Remove any existing insertion lines
            this.cleanupInsertionLines();

            // Get all children of the container, excluding the dragged element
            let children = Array.from(container.children).filter(
                (child) =>
                    !child.classList.contains('insertion-line') &&
                    (child.classList.contains('category-wrapper') ||
                        child.classList.contains('category-item') ||
                        child.classList.contains('type-item')),
            );

            // Exclude the currently dragged element from the children list
            const draggedElement = this.dragData.draggedElement;
            if (draggedElement) {
                children = children.filter((child) => {
                    // For types, check data-type-id
                    if (this.dragData.draggedType === 'type') {
                        return child.dataset.typeId !== this.dragData.draggedId;
                    }
                    // For categories, check the wrapper's data-category-id
                    if (this.dragData.draggedType === 'category') {
                        return child.dataset.categoryId !== this.dragData.draggedId;
                    }
                    return true;
                });
            }

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
                yPosition = prevChildRect.bottom - containerRect.top + (nextChildRect.top - prevChildRect.bottom) / 2;
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
            document.querySelectorAll('.insertion-line').forEach((line) => line.remove());
        },

        calculateInsertionPosition(container, mouseY) {
            // Get all valid children from the container
            let children = Array.from(container.children).filter(
                (child) =>
                    !child.classList.contains('insertion-line') &&
                    (child.classList.contains('category-wrapper') ||
                        child.classList.contains('category-item') ||
                        child.classList.contains('type-item')),
            );

            // Filter out the dragged element to get accurate positioning
            const draggedElement = this.dragData.draggedElement;
            if (draggedElement) {
                children = children.filter((child) => {
                    // For types, compare data-type-id
                    if (this.dragData.draggedType === 'type') {
                        const childId = String(child.dataset.typeId || '');
                        const draggedId = String(this.dragData.draggedId || '');
                        return childId !== draggedId;
                    }
                    // For categories, compare data-category-id on the wrapper
                    if (this.dragData.draggedType === 'category') {
                        const childId = String(child.dataset.categoryId || '');
                        const draggedId = String(this.dragData.draggedId || '');
                        return childId !== draggedId;
                    }
                    return true;
                });
            }

            if (children.length === 0) {
                return 0;
            }

            // Find the insertion position based on mouse Y coordinate
            for (let i = 0; i < children.length; i++) {
                const rect = children[i].getBoundingClientRect();
                const childCenterY = rect.top + rect.height / 2;

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
                        if (
                            this.draggingCategoryWouldViolateDepth(this.dragData.draggedId, prospectiveParentId) ||
                            this.wouldExceedDepthLimit(actualTarget)
                        ) {
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
                            insertIndex: insertIndex,
                        };
                    }
                } else {
                    const prospectiveParentId = actualTarget.dataset.categoryId;
                    if (
                        this.draggingCategoryWouldViolateDepth(this.dragData.draggedId, prospectiveParentId) ||
                        this.wouldExceedDepthLimit(actualTarget)
                    ) {
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

                if (
                    draggedContainer &&
                    targetContainer &&
                    draggedContainer.dataset.categoryId === targetContainer.dataset.categoryId
                ) {
                    const insertIndex = this.calculateInsertionPosition(targetContainer, e.clientY);
                    return {
                        type: 'insert',
                        container: targetContainer,
                        insertIndex: insertIndex,
                    };
                } else {
                    return null;
                }
            }

            if (this.dragData.draggedType === 'type') {
                const typeContainer = target.closest('[data-sortable="types"]');

                if (typeContainer) {
                    const draggedContainer = this.dragData.draggedElement?.closest('[data-sortable="types"]');
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
                    target: categoryItem,
                };
            }

            return null; // Invalid drop
        },

        cleanupDropIndicators() {
            this.cleanupInsertionLines();
            document.querySelectorAll('.nest-target').forEach((target) => {
                target.classList.remove(
                    'nest-target',
                    'bg-primary-500/10',
                    'border-primary-500/50',
                    'border-2',
                    'rounded-lg',
                    'relative',
                );
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
            // Remove the category from its current location
            const category = this.findAndRemoveCategory(categoryId);
            if (!category) return;

            category.parent_id = newParentId;

            // The insertIndex from calculateInsertionPosition is already correct
            // because it excluded the dragged element from the calculation
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
            // Remove the type from its current location
            const type = this.findAndRemoveType(typeId);
            if (!type) return;

            type.category_id = newCategoryId;

            // The insertIndex from calculateInsertionPosition is already correct
            // because it excluded the dragged element from the calculation
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
                    if (category.types && category.types.some((type) => type.id === typeId)) {
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

            if (
                this.treeData.uncategorized_types &&
                this.treeData.uncategorized_types.some((type) => type.id === typeId)
            ) {
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
                const type = this.treeData.uncategorized_types.find((t) => t.id === typeId);
                if (type) return type;
            }

            // Search in categories recursively
            const findInCategories = (categories) => {
                for (const category of categories) {
                    if (category.types) {
                        const type = category.types.find((t) => t.id === typeId);
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
            const rootIndex = (this.treeData.categories || []).findIndex((c) => c.id === categoryId);
            if (rootIndex !== -1) {
                return this.treeData.categories.splice(rootIndex, 1)[0];
            }

            return this.findAndRemoveCategoryRecursive(categoryId, this.treeData.categories || []);
        },

        findAndRemoveCategoryRecursive(categoryId, categories) {
            for (const category of categories) {
                if (category.children) {
                    const childIndex = category.children.findIndex((c) => c.id === categoryId);
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
            const uncategorizedIndex = (this.treeData.uncategorized_types || []).findIndex((t) => t.id === typeId);
            if (uncategorizedIndex !== -1) {
                return this.treeData.uncategorized_types.splice(uncategorizedIndex, 1)[0];
            }

            return this.findAndRemoveTypeRecursive(typeId, this.treeData.categories || []);
        },

        findAndRemoveTypeRecursive(typeId, categories) {
            for (const category of categories) {
                if (category.types) {
                    const typeIndex = category.types.findIndex((t) => t.id === typeId);
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
                await new Promise((resolve) => setTimeout(resolve, 100));

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
                        sort: index + 1,
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
                                sort: typeIndex + 1,
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
                            sort: index + 1,
                        });
                    }
                });
            }
        },

        extractUpdatedItems(categories, updatedCategories, updatedTypes) {
            categories.forEach((category) => {
                // Only check existing categories (not temp ones)
                if (!(typeof category.id === 'string' && category.id.startsWith('temp_'))) {
                    const originalCategory = this.findOriginalCategoryById(category.id);
                    if (originalCategory && originalCategory.name !== category.name) {
                        updatedCategories.push({
                            id: category.id,
                            name: category.name,
                        });
                    }
                }

                // Check types in this category
                if (category.types) {
                    category.types.forEach((type) => {
                        if (!(typeof type.id === 'string' && type.id.startsWith('temp_'))) {
                            const originalType = this.findOriginalTypeById(type.id);
                            if (originalType && originalType.name !== type.name) {
                                updatedTypes.push({
                                    id: type.id,
                                    name: type.name,
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
                this.treeData.uncategorized_types.forEach((type) => {
                    if (!(typeof type.id === 'string' && type.id.startsWith('temp_'))) {
                        const originalType = this.findOriginalTypeById(type.id);
                        if (originalType && originalType.name !== type.name) {
                            updatedTypes.push({
                                id: type.id,
                                name: type.name,
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
                const type = this.originalTreeData.uncategorized_types.find((t) => t.id === typeId);
                if (type) return type;
            }

            // Search in categories recursively
            const findInCategories = (categories) => {
                for (const category of categories) {
                    if (category.types) {
                        const type = category.types.find((t) => t.id === typeId);
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
                types: [],
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

            const typeHasRequests = (category.types || []).some((type) => (type.service_requests_count ?? 0) > 0);
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
        },
    }));
});
