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

import { useQuery } from '@pinia/colada';
import { computed, nextTick, onMounted, ref, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { apiPostUrl } from '../Services/api.js';
import { useConfigStore } from '../Stores/config.js';

export const searchFilterTabs = [
    { label: 'All Articles', value: 'all-articles' },
    { label: 'Featured', value: 'featured' },
    { label: 'Most Viewed', value: 'most-viewed' },
];

/**
 * Shared knowledge management search behaviour, used by both the homepage and the
 * category page: keeps `searchQuery`, `selectedTags`, `filter` and `page` in sync
 * with the route query params (so searches are shareable / survive back-forward
 * navigation), fetches paginated results from the signed search endpoint, and
 * exposes results already mapped with router `to` targets for the shared
 * `SearchResults` component.
 *
 * The URL is kept in sync via the History API rather than `router.push`/`replace`
 * so that syncing search state never re-runs the route's data loaders or flashes
 * the navigation progress bar.
 */
export function useKnowledgeManagementSearch() {
    const route = useRoute();
    const router = useRouter();
    const config = useConfigStore();

    const searchQuery = ref('');
    const activeSearchQuery = ref('');
    const selectedTags = ref([]);
    const activeTags = ref([]);
    const filter = ref('all-articles');
    const globalSearchInput = ref(null);

    const currentPage = ref(1);

    const isSearchActive = computed(
        () => !!(activeSearchQuery.value && activeSearchQuery.value.trim()) || activeTags.value.length > 0,
    );

    const pageQuery = useQuery({
        key: () => [
            'knowledge-management',
            'search',
            activeSearchQuery.value,
            activeTags.value.join(','),
            filter.value,
            currentPage.value,
        ],
        query: () =>
            apiPostUrl(config.searchUrl, {
                search: JSON.stringify(activeSearchQuery.value ?? ''),
                tags: activeTags.value.join(','),
                filter: filter.value,
                page: currentPage.value,
            }),
        enabled: isSearchActive,
        staleTime: 1000 * 60 * 5,
    });

    const shownEnvelope = ref(null);

    watch(
        pageQuery.data,
        (envelope) => {
            if (envelope) {
                shownEnvelope.value = envelope;
            } else if (!isSearchActive.value) {
                shownEnvelope.value = null;
            }
        },
        { immediate: true },
    );

    // Only true before the first result is shown; filter/page switches keep showing `shownEnvelope`.
    const loadingResults = computed(() => isSearchActive.value && shownEnvelope.value === null);

    const searchResultArticles = computed(() =>
        (shownEnvelope.value?.data?.articles?.data ?? []).map((article) => ({
            ...article,
            key: article.id,
            to: { name: 'view-article', params: { categorySlug: article.categorySlug, articleId: article.id } },
        })),
    );

    const searchResultCategories = computed(() =>
        (shownEnvelope.value?.data?.categories ?? []).map((category) => ({
            ...category,
            key: category.slug,
            to: { name: 'view-category', params: { categorySlug: category.slug } },
        })),
    );

    const lastPage = computed(() => shownEnvelope.value?.data?.articles?.meta?.last_page ?? 1);
    const totalArticles = computed(() => shownEnvelope.value?.data?.articles?.meta?.total ?? 0);
    const fromArticle = computed(() => shownEnvelope.value?.data?.articles?.meta?.from ?? 0);
    const toArticle = computed(() => shownEnvelope.value?.data?.articles?.meta?.to ?? 0);

    function syncUrl() {
        const resolved = router.resolve({
            name: route.name,
            params: route.params,
            query: {
                ...route.query,
                page: currentPage.value > 1 ? currentPage.value : undefined,
                search: activeSearchQuery.value || undefined,
                tags: activeTags.value.join(',') || undefined,
                filter: filter.value && filter.value !== 'all-articles' ? filter.value : undefined,
            },
        });

        history.replaceState(history.state, '', resolved.href);
    }

    function toggleTag(tag) {
        if (selectedTags.value.includes(tag)) {
            selectedTags.value = selectedTags.value.filter((selectedTag) => selectedTag !== tag);
        } else {
            selectedTags.value = [...selectedTags.value, tag];
        }
    }

    function changeSearchFilter(value) {
        if (filter.value !== value) {
            filter.value = value;
            currentPage.value = 1;
            syncUrl();
        }
    }

    function fetchPage(page) {
        if (page !== currentPage.value) {
            currentPage.value = page;
            syncUrl();
        }
    }

    function fetchNextPage() {
        if (currentPage.value < lastPage.value) {
            fetchPage(currentPage.value + 1);
        }
    }

    function fetchPreviousPage() {
        if (currentPage.value > 1) {
            fetchPage(currentPage.value - 1);
        }
    }

    let timerId;
    watch(
        () => [searchQuery.value, [...selectedTags.value]],
        ([newSearch, newTags]) => {
            if (timerId) clearTimeout(timerId);
            timerId = setTimeout(() => {
                const searchChanged = newSearch !== activeSearchQuery.value;
                const tagsChanged = newTags.join(',') !== activeTags.value.join(',');

                if (searchChanged || tagsChanged) {
                    shownEnvelope.value = null;
                    activeSearchQuery.value = newSearch;
                    activeTags.value = newTags;
                    currentPage.value = 1;
                    syncUrl();
                }
            }, 500);
        },
    );

    // Keep local state aligned with back/forward navigation that changes the query.
    watch(
        () => route.query.search,
        (newSearch) => {
            if ((newSearch || '') !== (searchQuery.value || '')) {
                searchQuery.value = newSearch || '';
            }
        },
    );

    onMounted(() => {
        const initialSearch = route.query.search || '';
        const initialTags = route.query.tags ? route.query.tags.split(',') : [];

        filter.value = route.query.filter || 'all-articles';
        currentPage.value = parseInt(route.query.page) || 1;

        if (initialSearch || initialTags.length > 0) {
            searchQuery.value = initialSearch;
            activeSearchQuery.value = initialSearch;
            selectedTags.value = initialTags;
            activeTags.value = initialTags;

            nextTick(() => globalSearchInput.value?.focus());
        }
    });

    return {
        searchQuery,
        selectedTags,
        filter,
        loadingResults,
        globalSearchInput,
        isSearchActive,
        searchResultArticles,
        searchResultCategories,
        currentPage,
        lastPage,
        totalArticles,
        fromArticle,
        toArticle,
        toggleTag,
        changeSearchFilter,
        fetchPage,
        fetchNextPage,
        fetchPreviousPage,
    };
}
