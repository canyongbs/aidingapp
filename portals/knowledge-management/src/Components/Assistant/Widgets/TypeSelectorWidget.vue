<!--
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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
-->
<script setup>
    import { computed, ref } from 'vue';
    import TypeCategory from './TypeCategory.vue';

    const props = defineProps({
        params: {
            type: Object,
            required: true,
        },
    });

    const emit = defineEmits(['submit', 'cancel']);

    const searchQuery = ref('');
    const suggestion = computed(() => props.params.suggestion);
    const typesTree = computed(() => props.params.types_tree || []);
    
    // Auto-expand browse section if there's no suggestion
    const showBrowse = ref(!props.params.suggestion);

    const filteredTree = computed(() => {
        if (!searchQuery.value.trim()) {
            return typesTree.value;
        }

        const query = searchQuery.value.toLowerCase();
        return filterCategories(typesTree.value, query);
    });

    const filterCategories = (categories, query) => {
        return categories
            .map((category) => {
                const matchingTypes = (category.types || []).filter(
                    (type) =>
                        type.name.toLowerCase().includes(query) ||
                        (type.description && type.description.toLowerCase().includes(query)),
                );

                const filteredChildren = filterCategories(category.children || [], query);

                if (matchingTypes.length > 0 || filteredChildren.length > 0) {
                    return {
                        ...category,
                        types: matchingTypes,
                        children: filteredChildren,
                    };
                }

                return null;
            })
            .filter(Boolean);
    };

    const selectType = (type) => {
        emit('submit', {
            type: 'type_selection',
            type_id: type.type_id,
            display_text: `Selected: ${type.name}`,
        });
    };
</script>

<template>
    <div class="bg-gray-50 rounded-lg border border-gray-200 overflow-hidden">
        <div class="p-3 border-b border-gray-200 bg-white">
            <h3 class="text-sm font-medium text-gray-900">Select Request Type</h3>
        </div>

        <div class="p-3 space-y-3">
            <!-- AI Suggestion -->
            <div v-if="suggestion" class="space-y-2">
                <p class="text-xs text-gray-600">Based on your message, I suggest:</p>
                <button
                    @click="selectType(suggestion)"
                    class="w-full text-left p-3 bg-brand-50 border border-brand-200 rounded-lg hover:bg-brand-100 transition-colors"
                >
                    <span class="font-medium text-brand-900">{{ suggestion.name }}</span>
                    <p v-if="suggestion.description" class="text-xs text-brand-700 mt-1">
                        {{ suggestion.description }}
                    </p>
                </button>
            </div>

            <!-- Browse Other Options -->
            <div v-if="typesTree.length > 0">
                <button
                    v-if="suggestion"
                    @click="showBrowse = !showBrowse"
                    class="text-xs text-gray-600 hover:text-gray-900 flex items-center gap-1"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                        class="w-4 h-4 transition-transform"
                        :class="{ 'rotate-90': showBrowse }"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    {{ showBrowse ? 'Hide other options' : 'Browse other options' }}
                </button>

                <div v-if="showBrowse" class="space-y-2" :class="{ 'mt-2': suggestion }">
                    <!-- Search -->
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Search request types..."
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-brand-500 focus:border-brand-500"
                    />

                    <!-- Category Tree -->
                    <div class="max-h-48 overflow-y-auto space-y-1">
                        <TypeCategory
                            v-for="(category, index) in filteredTree"
                            :key="category.category_id || `uncategorized-${index}`"
                            :category="category"
                            @select="selectType"
                        />

                        <p v-if="filteredTree.length === 0" class="text-xs text-gray-500 text-center py-2">
                            No matching request types found
                        </p>
                    </div>
                </div>
            </div>

            <!-- Cancel Button -->
            <button
                @click="emit('cancel')"
                class="w-full px-3 py-2 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-md transition-colors"
            >
                Cancel
            </button>
        </div>
    </div>
</template>
