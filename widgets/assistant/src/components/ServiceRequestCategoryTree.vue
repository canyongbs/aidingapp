<!--
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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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
-->
<script setup>
    import { ChevronDownIcon, ChevronRightIcon } from '@heroicons/vue/16/solid';
    import ServiceRequestCategoryTree from './ServiceRequestCategoryTree.vue';

    defineProps({
        categories: { type: Array, default: () => [] },
        types: { type: Array, default: () => [] },
        expandedCategories: { type: Set, required: true },
        selectedTypeId: { type: String, default: null },
        depth: { type: Number, default: 0 },
    });

    defineEmits(['toggle-category', 'select-type']);
</script>

<template>
    <div :class="depth > 0 ? 'ml-3 border-l border-gray-100 pl-2' : ''">
        <!-- Uncategorized / inline types at this level -->
        <button
            v-for="type in types"
            :key="type.id"
            @click="$emit('select-type', type)"
            :class="[
                'w-full flex items-center gap-2.5 text-left px-2.5 py-2.5 rounded-lg transition-all group',
                selectedTypeId === type.id
                    ? 'bg-brand-50 ring-1 ring-brand-200 relative z-10'
                    : 'hover:bg-gray-50',
            ]"
        >
            <!-- Checkbox indicator -->
            <span
                :class="[
                    'shrink-0 w-4 h-4 rounded flex items-center justify-center border-2 transition-all',
                    selectedTypeId === type.id
                        ? 'border-brand-500 bg-brand-500'
                        : 'border-gray-300 bg-white group-hover:border-gray-400',
                ]"
            >
                <svg
                    v-if="selectedTypeId === type.id"
                    viewBox="0 0 12 12"
                    class="w-2.5 h-2.5 text-white"
                    fill="none"
                >
                    <path
                        d="M2 6l3 3 5-5"
                        stroke="currentColor"
                        stroke-width="1.5"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />
                </svg>
            </span>

            <span
                :class="[
                    'text-sm font-medium truncate',
                    selectedTypeId === type.id ? 'text-brand-700' : 'text-gray-800',
                ]"
            >
                {{ type.name }}
            </span>
        </button>

        <!-- Categories -->
        <template v-for="category in categories" :key="category.id">
            <!-- Category header row (toggle) -->
            <button
                @click="$emit('toggle-category', category.id)"
                class="w-full flex items-center gap-2.5 text-left px-2.5 py-2.5 rounded-lg hover:bg-gray-50 transition-all group"
            >
                <component
                    :is="expandedCategories.has(category.id) ? ChevronDownIcon : ChevronRightIcon"
                    class="w-4 h-4 text-gray-400 shrink-0 transition-transform"
                />
                <span class="text-sm font-medium text-gray-700 group-hover:text-gray-900 truncate flex-1">
                    {{ category.name }}
                </span>
                <span class="text-xs text-gray-400 shrink-0 tabular-nums">
                    {{ (category.types?.length ?? 0) + (category.children?.length ?? 0) }}
                </span>
            </button>

            <!-- Expanded category contents -->
            <div v-if="expandedCategories.has(category.id)" class="mt-0.5 mb-1">
                <ServiceRequestCategoryTree
                    :categories="category.children || []"
                    :types="category.types || []"
                    :expanded-categories="expandedCategories"
                    :selected-type-id="selectedTypeId"
                    :depth="depth + 1"
                    @toggle-category="$emit('toggle-category', $event)"
                    @select-type="$emit('select-type', $event)"
                />
            </div>
        </template>
    </div>
</template>
