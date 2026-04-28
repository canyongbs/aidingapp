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
            const token = localStorage.getItem('token');
            const { data } = await axios.get(serviceRequestTypesUrl, {
                headers: token ? { Authorization: `Bearer ${token}` } : {},
            });
            rawData.value = data;
        } catch {
            loadError.value = true;
        } finally {
            isLoading.value = false;
        }
    });

    const filteredTree = computed(() => {
        if (!rawData.value) return null;
        const q = searchQuery.value.toLowerCase().trim();
        if (!q) return rawData.value;

        const words = q.split(/\s+/).filter(Boolean);

        function textMatches(text) {
            const t = (text ?? '').toLowerCase();
            return words.every((w) => t.includes(w));
        }

        function anyTextMatches(...texts) {
            const combined = texts.join(' ').toLowerCase();
            return words.every((w) => combined.includes(w));
        }

        function filterCats(cats, ancestorNames = []) {
            return cats.flatMap((cat) => {
                const lineage = [...ancestorNames, cat.name];
                const catMatches = textMatches(cat.name);
                const children = filterCats(cat.children || [], lineage);
                const types = cat.types.filter(
                    (t) => catMatches || anyTextMatches(t.name, t.description ?? '', ...lineage),
                );
                if (catMatches || types.length || children.length) {
                    return [{ ...cat, types: catMatches ? cat.types : types, children }];
                }
                return [];
            });
        }

        return {
            types: rawData.value.types.filter((t) => anyTextMatches(t.name, t.description ?? '')),
            categories: filterCats(rawData.value.categories),
        };
    });

    const hasResults = computed(() => {
        if (!filteredTree.value) return false;
        return filteredTree.value.types.length > 0 || filteredTree.value.categories.length > 0;
    });

    watch(searchQuery, (q) => {
        if (!q.trim() || !filteredTree.value) return;
        const s = new Set(expandedCategories.value);

        function expandAll(cats) {
            for (const cat of cats) {
                s.add(cat.id);
                if (cat.children?.length) expandAll(cat.children);
            }
        }

        expandAll(filteredTree.value.categories);
        expandedCategories.value = s;
    });

    const selectedPriorityObject = computed(
        () => selectedType.value?.priorities?.find((p) => p.id === selectedPriority.value) ?? null,
    );

    function toggleCategory(id) {
        const s = new Set(expandedCategories.value);
        s.has(id) ? s.delete(id) : s.add(id);
        expandedCategories.value = s;
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
