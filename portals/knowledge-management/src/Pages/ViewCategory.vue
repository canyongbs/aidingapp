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
    import BaseButton from '@common/BaseButton.vue';
    import Breadcrumbs from '@common/portal/Breadcrumbs.vue';
    import Article from '@common/portal/category/Article.vue';
    import SubCategories from '@common/portal/category/SubCategories.vue';
    import EmptyState from '@common/portal/EmptyState.vue';
    import HeroSearch from '@common/portal/HeroSearch.vue';
    import Page from '@common/portal/Page.vue';
    import Pagination from '@common/portal/Pagination.vue';
    import SearchResults from '@common/portal/SearchResults.vue';
    import Subheading from '@common/portal/Subheading.vue';
    import Tabs from '@common/portal/Tabs.vue';
    import { DocumentTextIcon } from '@heroicons/vue/24/outline';
    import { computed, watch } from 'vue';
    import { useRoute, useRouter } from 'vue-router';
    import LoadFailedState from '../Components/LoadFailedState.vue';
    import { useKnowledgeManagementSearch } from '../Composables/useKnowledgeManagementSearch.js';
    import { usePaginatedEnvelope } from '../Composables/usePaginatedEnvelope.js';
    import { apiGet } from '../Services/api.js';
    import { articleRoute, categoryRoute } from '../Services/KnowledgeBaseRoutes.js';
    import { useCategoryData, useTagsData } from './loaders.js';

    const route = useRoute();
    const router = useRouter();

    const { data: categoryResponse } = useCategoryData();
    const { data: tags } = useTagsData();

    const category = computed(() => categoryResponse.value?.category ?? null);

    // Redirects to the canonical slug(s) returned by the API (e.g. if the URL slug
    // casing doesn't exactly match), preserving the current route's shape.
    watch(
        category,
        (categoryValue) => {
            if (!categoryValue) return;

            const canonicalRoute = categoryRoute(categoryValue);

            if (router.resolve(canonicalRoute).path !== route.path) {
                router.replace({ ...canonicalRoute, query: { ...route.query } });
            }
        },
        { immediate: true },
    );

    const {
        searchQuery,
        selectedTags,
        filter: searchFilter,
        loadingResults: loadingSearchResults,
        globalSearchInput,
        isSearchActive,
        orderedFilterTabs: categoryTabs,
        toggleTag,
        changeSearchFilter,
        searchResultArticles,
        searchResultCategories,
        currentPage: searchCurrentPage,
        lastPage: searchLastPage,
        totalArticles,
        fromArticle,
        toArticle,
        fetchNextPage: fetchNextSearchPage,
        fetchPreviousPage: fetchPreviousSearchPage,
        fetchPage: fetchSearchPage,
    } = useKnowledgeManagementSearch();

    const subCategoriesWithRoutes = computed(() =>
        (category.value?.subCategories ?? []).map((subCategory) => ({
            ...subCategory,
            key: subCategory.slug,
            to: categoryRoute(subCategory),
        })),
    );

    // Tab filter for browsing a category's own articles. Page 1 of every filter tab arrives together via the route data
    // loader; any other page is fetched on demand. The filter and page live in the URL.
    const activeFilter = computed(() => route.query.filter || categoryTabs.value[0]?.value || 'all-articles');
    const currentPage = computed({
        get: () => parseInt(route.query.page) || 1,
        set: (page) => pushArticlesQuery(page, activeFilter.value),
    });

    function firstPageFor(filter) {
        const key =
            filter === 'featured'
                ? 'featured_articles'
                : filter === 'most-viewed'
                  ? 'most_viewed_articles'
                  : 'all_articles';

        return categoryResponse.value?.[key] ?? null;
    }

    const {
        shownEnvelope: shownArticles,
        isFetching: isFetchingArticles,
        loadFailed,
        pagination,
        refetch,
    } = usePaginatedEnvelope({
        initialEnvelope: computed(() => firstPageFor(activeFilter.value)),
        usesInitialEnvelope: (page) => page === 1,
        key: (page) => [
            'knowledge-management',
            'category-articles',
            String(route.params.categorySlug),
            activeFilter.value,
            page,
        ],
        query: (page) =>
            apiGet(`/categories/${route.params.categorySlug}`, { filter: activeFilter.value, page }).then(
                (response) => response.articles,
            ),
        currentPage,
    });

    const articlesWithRoutes = computed(() =>
        (shownArticles.value?.data ?? []).map((article) => ({
            ...article,
            key: article.id,
            to: articleRoute(article),
        })),
    );

    const breadcrumbs = computed(() => {
        if (category.value?.parentCategory) {
            const parentRoute = categoryRoute(category.value.parentCategory);

            return [
                {
                    name: category.value.parentCategory.name,
                    route: parentRoute.name,
                    params: parentRoute.params,
                },
            ];
        }

        return [];
    });

    function pushArticlesQuery(page, filter) {
        router.push({
            name: route.name,
            params: route.params,
            query: {
                ...route.query,
                page,
                filter: filter === categoryTabs.value[0]?.value ? undefined : filter,
            },
        });
    }

    function changeFilter(value) {
        pushArticlesQuery(1, value);
    }
</script>

<template>
    <Page>
        <template #heading> Need help? </template>

        <template #description> Search our knowledge base for advice and answers </template>

        <template #belowHeaderContent>
            <HeroSearch
                ref="globalSearchInput"
                v-model="searchQuery"
                :tags="tags ?? {}"
                :selectedTags="selectedTags"
                @toggle-tag="toggleTag"
            />
        </template>

        <template #breadcrumbs>
            <Breadcrumbs v-if="category && !isSearchActive" :currentCrumb="category.name" :breadcrumbs="breadcrumbs" />
        </template>

        <EmptyState v-if="!category">
            <template #heading>Category Not Found</template>
            <template #description>The category you are looking for does not exist or has been removed.</template>
            <template #actions>
                <BaseButton tag="router-link" :to="{ name: 'home' }" color="gray" size="md"> Return Home </BaseButton>
            </template>
        </EmptyState>
        <div v-else>
            <div class="flex flex-col gap-8">
                <div v-if="isSearchActive" class="flex flex-col gap-6">
                    <SearchResults
                        :searchQuery="searchQuery"
                        :articles="searchResultArticles"
                        :categories="searchResultCategories"
                        :loadingResults="loadingSearchResults"
                        :filter-tabs="categoryTabs"
                        @change-filter="changeSearchFilter"
                        :selected-filter="searchFilter"
                        :currentPage="searchCurrentPage"
                        :lastPage="searchLastPage"
                        :fromItem="fromArticle"
                        :toItem="toArticle"
                        :totalItems="totalArticles"
                        @fetchNextPage="fetchNextSearchPage"
                        @fetchPreviousPage="fetchPreviousSearchPage"
                        @fetchPage="fetchSearchPage"
                    >
                    </SearchResults>
                </div>
                <div v-else class="flex flex-col gap-6">
                    <div class="flex flex-col gap-4">
                        <div class="flex flex-col gap-1">
                            <Subheading :title="category.name" />
                            <p v-if="category.description" class="text-sm text-gray-500">{{ category.description }}</p>
                        </div>
                        <SubCategories
                            v-if="category.subCategories.length > 0"
                            :subCategories="subCategoriesWithRoutes"
                        ></SubCategories>
                        <div class="flex flex-col overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-gray-950/5">
                            <Tabs
                                :tabs="categoryTabs"
                                :modelValue="activeFilter"
                                @update:modelValue="changeFilter"
                                :contained="true"
                            />

                            <LoadFailedState v-if="loadFailed" heading="Could not load articles" @retry="refetch" />
                            <div
                                v-else-if="articlesWithRoutes.length > 0"
                                :class="['transition-opacity', isFetchingArticles && 'opacity-60']"
                                :aria-busy="isFetchingArticles ? 'true' : undefined"
                            >
                                <ul role="list" class="divide-y">
                                    <li v-for="article in articlesWithRoutes" :key="article.key">
                                        <Article
                                            :to="article.to"
                                            :name="article.name"
                                            :tags="article.tags"
                                            :featured="article.featured"
                                        />
                                    </li>
                                </ul>
                                <Pagination v-bind="pagination" />
                            </div>
                            <section v-else class="px-6 py-4 flex items-start gap-x-4">
                                <div class="flex size-12 items-center justify-center rounded-full bg-gray-100">
                                    <DocumentTextIcon class="size-6 text-gray-400" />
                                </div>

                                <div class="flex-1">
                                    <h4 class="text-base font-semibold leading-6 text-gray-950">No articles found</h4>

                                    <p class="mt-1 text-sm text-gray-500">No articles found in this category.</p>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Page>
</template>
