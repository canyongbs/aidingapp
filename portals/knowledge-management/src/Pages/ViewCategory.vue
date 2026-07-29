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
    import PageCard from '@common/portal/PageCard.vue';
    import Pagination from '@common/portal/Pagination.vue';
    import SearchResults from '@common/portal/SearchResults.vue';
    import Subheading from '@common/portal/Subheading.vue';
    import Tabs from '@common/portal/Tabs.vue';
    import { DocumentTextIcon } from '@heroicons/vue/24/outline';
    import { useQuery } from '@pinia/colada';
    import { computed, ref, watch } from 'vue';
    import { useRoute, useRouter } from 'vue-router';
    import { searchFilterTabs, useKnowledgeManagementSearch } from '../Composables/useKnowledgeManagementSearch.js';
    import { apiGet } from '../Services/api.js';
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

            if (route.params.parentCategorySlug) {
                router.replace({
                    name: 'view-subcategory',
                    params: {
                        parentCategorySlug: categoryValue.parentCategory.slug,
                        categorySlug: categoryValue.slug,
                    },
                    query: { ...route.query },
                });
            } else {
                router.replace({
                    name: 'view-category',
                    params: { categorySlug: categoryValue.slug },
                    query: { ...route.query },
                });
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
            to: { name: 'view-category', params: { categorySlug: subCategory.slug } },
        })),
    );

    // Tab filter for browsing a category's own articles. The route data loader always
    // resolves page 1 of the default ("all articles") view, so any other filter + page
    // combination is fetched (and cached) client-side via Pinia Colada.
    const activeFilter = ref(route.query.filter || 'all-articles');
    const currentPage = ref(parseInt(route.query.page) || 1);
    const isDefaultView = computed(() => activeFilter.value === 'all-articles' && currentPage.value === 1);

    function syncUrl() {
        const resolved = router.resolve({
            name: route.name,
            params: route.params,
            query: {
                ...route.query,
                page: currentPage.value > 1 ? currentPage.value : undefined,
                filter: activeFilter.value === 'all-articles' ? undefined : activeFilter.value,
            },
        });
        history.replaceState(history.state, '', resolved.href);
    }

    const pageQuery = useQuery({
        key: () => [
            'knowledge-management',
            'category-articles',
            String(route.params.categorySlug),
            activeFilter.value,
            currentPage.value,
        ],
        query: () =>
            apiGet(`/categories/${route.params.categorySlug}`, { filter: activeFilter.value, page: currentPage.value }),
        enabled: () => !isDefaultView.value,
    });

    const currentArticles = computed(() =>
        isDefaultView.value ? (categoryResponse.value?.articles ?? null) : (pageQuery.data.value?.articles ?? null),
    );

    // Keep the previously rendered page visible while a new page/filter loads so
    // content never disappears mid-pagination.
    const shownArticles = ref(null);
    watch(
        currentArticles,
        (articles) => {
            if (articles) {
                shownArticles.value = articles;
            }
        },
        { immediate: true },
    );

    const loadingArticles = computed(() => !isDefaultView.value && pageQuery.isLoading.value);
    const loadingPage = computed(() => (loadingArticles.value ? currentPage.value : null));

    const articlesWithRoutes = computed(() =>
        (shownArticles.value?.data ?? []).map((article) => ({
            ...article,
            key: article.id,
            to: { name: 'view-article', params: { categorySlug: article.categorySlug, articleId: article.id } },
        })),
    );

    const articlePagination = computed(() => ({
        currentPage: shownArticles.value?.current_page ?? 1,
        lastPage: shownArticles.value?.last_page ?? 1,
        total: shownArticles.value?.total ?? 0,
        from: shownArticles.value?.from ?? 0,
        to: shownArticles.value?.to ?? 0,
    }));

    const breadcrumbs = computed(() => {
        if (category.value?.parentCategory) {
            return [
                {
                    name: category.value.parentCategory.name,
                    route: 'view-category',
                    params: { categorySlug: category.value.parentCategory.slug },
                },
            ];
        }

        return [];
    });

    function pushArticlesQuery(page, filter) {
        currentPage.value = page;
        activeFilter.value = filter;
        syncUrl();
    }

    function changeFilter(value) {
        pushArticlesQuery(1, value);
    }

    function goToPage(page) {
        if (page === articlePagination.value.currentPage || loadingArticles.value) {
            return;
        }

        pushArticlesQuery(page, activeFilter.value);
    }

    function fetchNextPage() {
        if (articlePagination.value.currentPage < articlePagination.value.lastPage) {
            goToPage(articlePagination.value.currentPage + 1);
        }
    }

    function fetchPreviousPage() {
        if (articlePagination.value.currentPage > 1) {
            goToPage(articlePagination.value.currentPage - 1);
        }
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

        <PageCard v-if="!category">
            <EmptyState>
                <template #heading>Category Not Found</template>
                <template #description>The category you are looking for does not exist or has been removed.</template>
                <template #actions>
                    <BaseButton :to="{ name: 'home' }" variant="outline"> Return to Home </BaseButton>
                </template>
            </EmptyState>
        </PageCard>
        <div v-else>
            <main class="flex flex-col gap-8">
                <div v-if="isSearchActive" class="flex flex-col gap-6">
                    <SearchResults
                        :searchQuery="searchQuery"
                        :articles="searchResultArticles"
                        :categories="searchResultCategories"
                        :loadingResults="loadingSearchResults"
                        :filter-tabs="searchFilterTabs"
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
                        <div class="flex flex-col overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
                            <Tabs
                                :tabs="searchFilterTabs"
                                :modelValue="activeFilter"
                                @update:modelValue="changeFilter"
                                :contained="true"
                            />

                            <div v-if="articlesWithRoutes.length > 0">
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
                                <Pagination
                                    :currentPage="articlePagination.currentPage"
                                    :lastPage="articlePagination.lastPage"
                                    :fromItem="articlePagination.from"
                                    :toItem="articlePagination.to"
                                    :totalItems="articlePagination.total"
                                    :loadingPage="loadingPage"
                                    @fetchNextPage="fetchNextPage"
                                    @fetchPreviousPage="fetchPreviousPage"
                                    @fetchPage="goToPage"
                                />
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
            </main>
        </div>
    </Page>
</template>
