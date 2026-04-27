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
-->
<script setup>
    import { MagnifyingGlassIcon, XMarkIcon } from '@heroicons/vue/16/solid';
    import { ref } from 'vue';
    import { useServiceRequestTypes } from '../../composables/useServiceRequestTypes.js';
    import ServiceRequestCategoryTree from '../ServiceRequestCategoryTree.vue';
    import TypeSelectFooter from './TypeSelectFooter.vue';

    const props = defineProps({
        serviceRequestTypesUrl: { type: String, required: true },
    });

    const emit = defineEmits(['continue']);

    const activeTab = ref('new');

    const {
        isLoading,
        loadError,
        rawData,
        searchQuery,
        filteredTree,
        hasResults,
        expandedCategories,
        selectedType,
        selectedPriority,
        toggleCategory,
        selectType,
        clearSearch,
    } = useServiceRequestTypes(props.serviceRequestTypesUrl);

    function onContinue() {
        emit('continue', {
            type: selectedType.value,
            priority: selectedPriority.value,
            rawData: rawData.value,
        });
    }
</script>

<template>
    <!-- Tabs -->
    <div class="px-4 pt-4 pb-0 shrink-0">
        <div class="flex gap-1 bg-gray-100 rounded-xl p-1">
            <button
                @click="activeTab = 'new'"
                :class="[
                    'flex-1 text-sm font-medium py-2 rounded-lg transition-all',
                    activeTab === 'new' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700',
                ]"
            >
                New Issue
            </button>
            <button
                disabled
                class="flex-1 text-sm font-medium py-2 rounded-lg text-gray-300 cursor-not-allowed"
                title="Coming soon"
            >
                Existing Issue
            </button>
        </div>
    </div>

    <template v-if="activeTab === 'new'">
        <!-- Search -->
        <div class="px-4 pt-3 pb-2 shrink-0">
            <div class="relative">
                <MagnifyingGlassIcon
                    class="pointer-events-none absolute left-3 inset-y-0 my-auto w-4 h-4 text-gray-400"
                />
                <input
                    v-model="searchQuery"
                    type="text"
                    placeholder="Search request types…"
                    class="w-full bg-gray-50 border border-gray-200 rounded-lg pl-9 pr-8 py-2 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent transition-all"
                />
                <button
                    v-if="searchQuery"
                    @click="clearSearch"
                    class="absolute right-2.5 inset-y-0 my-auto text-gray-400 hover:text-gray-600 transition-colors"
                >
                    <XMarkIcon class="w-4 h-4" />
                </button>
            </div>
        </div>

        <!-- Tree -->
        <div class="flex-1 overflow-y-auto px-4 py-2">
            <div v-if="isLoading" class="flex flex-col items-center justify-center h-full gap-3 text-gray-400">
                <svg class="w-6 h-6 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z" />
                </svg>
                <span class="text-sm">Loading request types…</span>
            </div>

            <div v-else-if="loadError" class="flex flex-col items-center justify-center h-full gap-2 text-center px-4">
                <p class="text-sm font-medium text-gray-700">Couldn't load request types</p>
                <p class="text-xs text-gray-400">Please try closing and reopening the widget.</p>
            </div>

            <div
                v-else-if="searchQuery && !hasResults"
                class="flex flex-col items-center justify-center h-full gap-2 text-center px-4"
            >
                <p class="text-sm font-medium text-gray-700">No types match "{{ searchQuery }}"</p>
                <button @click="clearSearch" class="text-xs text-brand-500 hover:text-brand-600 font-medium">
                    Clear search
                </button>
            </div>

            <ServiceRequestCategoryTree
                v-else-if="filteredTree"
                :categories="filteredTree.categories"
                :types="filteredTree.types"
                :expanded-categories="expandedCategories"
                :selected-type-id="selectedType?.id ?? null"
                :depth="0"
                @toggle-category="toggleCategory"
                @select-type="selectType"
            />
        </div>

        <!-- Priority + Continue footer -->
        <Transition
            enter-active-class="transition-all duration-150 ease-out"
            enter-from-class="opacity-0 translate-y-2"
            enter-to-class="opacity-100 translate-y-0"
        >
            <TypeSelectFooter
                v-if="selectedType"
                :selected-type="selectedType"
                v-model="selectedPriority"
                @continue="onContinue"
            />
        </Transition>
    </template>
</template>
