/*
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
*/
import axios from 'axios';
import { computed, onMounted, ref, watch } from 'vue';
import { clearToken, getAuthHeaders } from '../utils/token.js';

export function useServiceRequestTypes(serviceRequestTypesUrl) {
    const isLoading = ref(true);
    const loadError = ref(false);
    const rawData = ref(null);
    const searchQuery = ref('');
    const expandedCategories = ref(new Set());
    const selectedType = ref(null);
    const selectedPriority = ref('');

    onMounted(async () => {
        try {
            const { data } = await axios.get(serviceRequestTypesUrl, {
                headers: getAuthHeaders(),
            });
            rawData.value = data;
        } catch {
            clearToken();
        } finally {
            isLoading.value = false;
        }
    });

    const filteredTree = computed(() => {
        if (!rawData.value) return null;
        const normalizedQuery = searchQuery.value.toLowerCase().trim();
        if (!normalizedQuery) return rawData.value;

        const words = normalizedQuery.split(/\s+/).filter(Boolean);

        function textMatches(text) {
            const lowercaseText = (text ?? '').toLowerCase();
            return words.every((word) => lowercaseText.includes(word));
        }

        function anyTextMatches(...texts) {
            const combined = texts.join(' ').toLowerCase();
            return words.every((word) => combined.includes(word));
        }

        function filterCategories(categoryNodes, ancestorNames = []) {
            return categoryNodes.flatMap((category) => {
                const lineage = [...ancestorNames, category.name];
                const categoryMatches = textMatches(category.name);
                const children = filterCategories(category.children || [], lineage);
                const types = category.types.filter(
                    (type) => categoryMatches || anyTextMatches(type.name, type.description ?? '', ...lineage),
                );
                if (categoryMatches || types.length || children.length) {
                    return [{ ...category, types: categoryMatches ? category.types : types, children }];
                }
                return [];
            });
        }

        return {
            types: rawData.value.types.filter((type) => anyTextMatches(type.name, type.description ?? '')),
            categories: filterCategories(rawData.value.categories),
        };
    });

    const hasResults = computed(() => {
        if (!filteredTree.value) return false;
        return filteredTree.value.types.length > 0 || filteredTree.value.categories.length > 0;
    });

    watch(searchQuery, (newQuery) => {
        if (!newQuery.trim() || !filteredTree.value) return;
        const expandedSet = new Set(expandedCategories.value);

        function expandAll(categoryNodes) {
            for (const category of categoryNodes) {
                expandedSet.add(category.id);
                if (category.children?.length) expandAll(category.children);
            }
        }

        expandAll(filteredTree.value.categories);
        expandedCategories.value = expandedSet;
    });

    const selectedPriorityObject = computed(
        () => selectedType.value?.priorities?.find((priority) => priority.id === selectedPriority.value) ?? null,
    );

    function toggleCategory(id) {
        const updatedSet = new Set(expandedCategories.value);
        updatedSet.has(id) ? updatedSet.delete(id) : updatedSet.add(id);
        expandedCategories.value = updatedSet;
    }

    function selectType(type) {
        selectedType.value = type;
        selectedPriority.value = '';
    }

    function clearSearch() {
        searchQuery.value = '';
    }

    return {
        isLoading,
        loadError,
        rawData,
        searchQuery,
        filteredTree,
        hasResults,
        expandedCategories,
        selectedType,
        selectedPriority,
        selectedPriorityObject,
        toggleCategory,
        selectType,
        clearSearch,
    };
}
