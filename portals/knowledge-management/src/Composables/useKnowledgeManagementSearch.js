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
import { computed, nextTick, onMounted, ref, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { apiPostUrl } from '../Services/api.js';
import { useConfigStore } from '../Stores/config.js';

export const searchFilterTabs = [
    { label: 'All Articles', value: 'all-articles' },
    { label: 'Featured', value: 'featured' },
    { label: 'Most Viewed', value: 'most-viewed' },
];

function debounce(func, delay) {
    let timerId;

    return function (...args) {
        if (timerId) clearTimeout(timerId);
        timerId = setTimeout(() => func(...args), delay);
    };
}

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
    const selectedTags = ref([]);
    const filter = ref('');
    const loadingResults = ref(false);
    const searchResults = ref(null);
    const globalSearchInput = ref(null);

    const currentPage = ref(1);
    const lastPage = ref(1);
    const totalArticles = ref(0);
    const fromArticle = ref(0);
    const toArticle = ref(0);

    const isSearchActive = computed(() => !!(searchQuery.value && searchQuery.value.trim()) || selectedTags.value.length > 0);

    const searchResultArticles = computed(() =>
        (searchResults.value?.data?.articles?.data ?? []).map((article) => ({
            ...article,
            key: article.id,
            to: { name: 'view-article', params: { categorySlug: article.categorySlug, articleId: article.id } },
        })),
    );

    const searchResultCategories = computed(() =>
        (searchResults.value?.data?.categories ?? []).map((category) => ({
            ...category,
            key: category.slug,
            to: { name: 'view-category', params: { categorySlug: category.slug } },
        })),
    );

    function setPagination(pagination) {
        currentPage.value = pagination.current_page;
        lastPage.value = pagination.last_page;
        totalArticles.value = pagination.total;
        fromArticle.value = pagination.from;
        toArticle.value = pagination.to;
    }

    function syncUrl() {
        const resolved = router.resolve({
            name: route.name,
            params: route.params,
            query: {
                ...route.query,
                page: currentPage.value > 1 ? currentPage.value : undefined,
                search: searchQuery.value || undefined,
                tags: selectedTags.value.join(',') || undefined,
                filter: filter.value || undefined,
            },
        });

        history.replaceState(history.state, '', resolved.href);
    }

    const fetchResults = debounce((page) => {
        if (!isSearchActive.value) {
            searchResults.value = null;
            loadingResults.value = false;

            return;
        }

        apiPostUrl(config.searchUrl, {
            search: JSON.stringify(searchQuery.value ?? ''),
            tags: selectedTags.value.join(','),
            filter: filter.value,
            page,
        })
            .then((body) => {
                searchResults.value = body;
                setPagination(body.data.articles.meta);
            })
            .finally(() => {
                loadingResults.value = false;
            });
    }, 500);

    function runSearch({ page = 1, focus = false } = {}) {
        if (!isSearchActive.value) {
            searchResults.value = null;
            loadingResults.value = false;

            return;
        }

        loadingResults.value = true;

        if (focus) {
            nextTick(() => globalSearchInput.value?.focus());
        }

        fetchResults(page);
    }

    function toggleTag(tag) {
        if (selectedTags.value.includes(tag)) {
            selectedTags.value = selectedTags.value.filter((selectedTag) => selectedTag !== tag);
        } else {
            selectedTags.value = [...selectedTags.value, tag];
        }
    }

    function changeSearchFilter(value) {
        filter.value = value;
        currentPage.value = 1;
        syncUrl();
        runSearch({ page: 1 });
    }

    function fetchPage(page) {
        if (page === currentPage.value) {
            return;
        }

        currentPage.value = page;
        syncUrl();
        runSearch({ page });
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

    // A change in the search box or selected tags resets to page 1 and refetches.
    watch(
        () => [searchQuery.value, [...selectedTags.value]],
        () => {
            currentPage.value = 1;
            syncUrl();
            runSearch({ page: 1 });
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

        filter.value = route.query.filter || '';
        currentPage.value = parseInt(route.query.page) || 1;

        if (initialSearch || initialTags.length > 0) {
            searchQuery.value = initialSearch;
            selectedTags.value = initialTags;

            runSearch({ page: currentPage.value, focus: true });
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
