<!--
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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
-->
<script setup>
    import { XMarkIcon } from '@heroicons/vue/20/solid/index.js';
    import { MagnifyingGlassIcon } from '@heroicons/vue/24/outline';
    import { computed, defineProps, ref, watch } from 'vue';
    import { useRoute, useRouter } from 'vue-router';
    import AppLoading from '../Components/AppLoading.vue';
    import Article from '../Components/Article.vue';
    import Badge from '../Components/Badge.vue';
    import Breadcrumbs from '../Components/Breadcrumbs.vue';
    import FilterComponent from '../Components/FilterComponent.vue';
    import Pagination from '../Components/Pagination.vue';
    import SearchResults from '../Components/SearchResults.vue';
    import SubCategories from '../Components/SubCategories.vue';
    import { consumer } from '../Services/Consumer.js';

    const route = useRoute();
    const router = useRouter();

    const props = defineProps({
        searchUrl: {
            type: String,
            required: true,
        },
        apiUrl: {
            type: String,
            required: true,
        },
        categories: {
            type: Object,
            required: true,
        },
        tags: {
            type: Object,
            required: true,
        },
    });

    const loadingResults = ref(true);
    const loadingeSearchResults = ref(true);
    const category = ref(null);
    const articles = ref(null);
    const searchQuery = ref('');
    const searchResults = ref(null);
    const selectedTags = ref([]);
    const currentPage = ref(1);
    const nextPageUrl = ref(null);
    const prevPageUrl = ref(null);
    const lastPage = ref(null);
    const totalArticles = ref(0);
    const fromArticle = ref(0);
    const toArticle = ref(0);
    const filter = ref('');
    const fromSearch = ref(false);

    const debounceSearch = debounce((value, page = 1) => {
        const { post } = consumer();
        fromSearch.value = true;
        if (!value && selectedTags.value.length < 1) {
            searchQuery.value = null;
            searchResults.value = null;
            return;
        }

        loadingeSearchResults.value = true;

        post(props.searchUrl, {
            search: JSON.stringify(value),
            tags: selectedTags.value.join(','),
            filter: filter.value,
            page: page,
        }).then((response) => {
            searchResults.value = response.data;
            loadingeSearchResults.value = false;
            setPagination(response.data.data.articles.meta);
        });
    }, 500);

    const setPagination = (pagination) => {
        currentPage.value = pagination.current_page;
        prevPageUrl.value = pagination.prev_page_url;
        nextPageUrl.value = pagination.next_page_url;
        lastPage.value = pagination.last_page;
        totalArticles.value = pagination.total;
        fromArticle.value = pagination.from;
        toArticle.value = pagination.to;
    };

    watch(searchQuery, (value) => {
        if (value == null) {
            fromSearch.value = false;
            getData(1);

            return;
        }
        debounceSearch(value);
    });

    watch(selectedTags, () => {
        debounceSearch(searchQuery.value);
    });

    function debounce(func, delay) {
        let timerId;
        return function (...args) {
            if (timerId) {
                clearTimeout(timerId);
            }
            timerId = setTimeout(() => {
                func(...args);
            }, delay);
        };
    }

    function toggleTag(tag) {
        if (selectedTags.value.includes(tag)) {
            selectedTags.value = selectedTags.value.filter((t) => t !== tag);
        } else {
            selectedTags.value = [...selectedTags.value, tag];
        }
    }

    const fetchNextPage = () => {
        currentPage.value = currentPage.value !== lastPage.value ? currentPage.value + 1 : lastPage.value;
        getData(currentPage.value);
    };

    const fetchPreviousPage = () => {
        currentPage.value = currentPage.value !== 1 ? currentPage.value - 1 : 1;
        getData(currentPage.value);
    };

    const fetchPage = (page) => {
        currentPage.value = page;
        getData(currentPage.value);
    };

    const changeFilter = (value) => {
        filter.value = value;
        getData(1);
    };

    const changeSearchFilter = (value) => {
        filter.value = value;
        debounceSearch(searchQuery.value);
    };

    const breadcrumbs = computed(() => {
        if (category.value.parentCategory) {
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

    watch(
        route,
        async function (newRouteValue) {
            searchQuery.value = '';
            fromSearch.value = false;
            await getData();
        },
        {
            immediate: true,
        },
    );

    async function getData(page = 1) {
        if (fromSearch.value) {
            debounceSearch(searchQuery.value, page);
            return;
        }

        loadingResults.value = true;

        const { get } = consumer();
        
        await get(props.apiUrl + '/categories/' + route.params.categorySlug, { page: page, filter: filter.value }).then((response) => {
            if(route.params.categorySlug && route.params.parentCategorySlug) {
                router.replace({
                    name: 'view-subcategory',
                    params: { parentCategorySlug: response.data.category.parentCategory.slug, categorySlug: response.data.category.slug },
                });
            } else if (route.params.categorySlug) {
                router.replace({ name: 'view-category', params: { categorySlug: response.data.category.slug } });
            }
            
            category.value = response.data.category;
            articles.value = response.data.articles.data;
            setPagination(response.data.articles);
            loadingResults.value = false;
        });
    }
</script>

<template>
    <div class="top-0 z-40 flex flex-col items-center bg-gray-50">
        <div class="bg-gradient-to-br from-primary-500 to-primary-800 w-full px-6">
            <div class="max-w-screen-xl flex flex-col gap-y-6 mx-auto py-8">
                <div class="flex flex-col gap-y-1 text-left">
                    <h3 class="text-3xl font-semibold text-white">Need help?</h3>
                    <p class="text-primary-100">Search our knowledge base for advice and answers</p>
                </div>

                <label for="search" class="sr-only">Search</label>
                <div class="relative rounded">
                    <div>
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 py-3">
                            <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
                        </div>
                        <input
                            type="search"
                            v-model="searchQuery"
                            id="search"
                            placeholder="Search for articles and categories"
                            class="block w-full rounded border-0 pl-12 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-2-- sm:text-sm sm:leading-6"
                            :class="{ 'rounded-b-none': tags.length > 0 }"
                        />
                    </div>
                </div>
                <details
                    v-if="tags.length > 0"
                    class="rounded rounded-t-none bg-white py-3 p-4 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-2-- sm:text-sm sm:leading-6"
                >
                    <summary v-if="selectedTags.length > 0">Tags ({{ selectedTags.length }} selected)</summary>
                    <summary v-else>Tags</summary>
                    <div class="flex flex-wrap gap-2">
                        <Badge
                            v-for="tag in tags"
                            :key="tag.id"
                            :value="tag.name"
                            class="cursor-pointer"
                            :class="{ 'bg-primary-600 text-white': selectedTags.includes(tag.id) }"
                            @click="toggleTag(tag.id)"
                        />
                    </div>
                </details>
            </div>
        </div>
    </div>
    <div class="sticky top-0 z-40 flex flex-col items-center bg-gray-50">
        <div class="w-full px-6">
            <div class="max-w-screen-xl flex flex-col gap-y-6 mx-auto py-8">
                <div v-if="loadingResults">
                    <AppLoading />
                </div>
                <div v-else>
                    <main class="flex flex-col gap-8">
                        <div v-if="searchQuery || selectedTags.length > 0" class="flex flex-col gap-6">
                            <SearchResults
                                :searchQuery="searchQuery"
                                :searchResults="searchResults"
                                :loadingResults="loadingeSearchResults"
                                @change-filter="changeSearchFilter"
                                :selected-filter="filter"
                                :currentPage="currentPage"
                                :lastPage="lastPage"
                                :fromArticle="fromArticle"
                                :toArticle="toArticle"
                                :totalArticles="totalArticles"
                                @fetchNextPage="fetchNextPage"
                                @fetchPreviousPage="fetchPreviousPage"
                                @fetchPage="fetchPage"
                            >
                            </SearchResults>
                        </div>
                        <div v-else class="flex flex-col gap-6">
                            <Breadcrumbs :currentCrumb="category.name" :breadcrumbs="breadcrumbs"></Breadcrumbs>
                            <div class="flex flex-col gap-6">
                                <h2 class="text-2xl font-bold text-primary-950">
                                    {{ category.name }}
                                </h2>
                                <SubCategories
                                    v-if="category.subCategories.length > 0"
                                    :subCategories="category.subCategories"
                                ></SubCategories>
                                <filter-component
                                    @change-filter="changeFilter"
                                    :selected-filter="filter"
                                ></filter-component>
                                <div>
                                    <div
                                        class="flex flex-col divide-y ring-1 ring-black/5 shadow-sm px-3 pt-3 pb-1 rounded bg-white"
                                    >
                                        <h3 class="text-lg font-semibold text-gray-800 px-3 pt-1 pb-3">
                                            Articles ({{ totalArticles }})
                                        </h3>

                                        <div v-if="articles.length > 0">
                                            <ul role="list" class="divide-y">
                                                <li v-for="article in articles" :key="article.id">
                                                    <Article :article="article" />
                                                </li>
                                            </ul>
                                            <Pagination
                                                :currentPage="currentPage"
                                                :lastPage="lastPage"
                                                :fromArticle="fromArticle"
                                                :toArticle="toArticle"
                                                :totalArticles="totalArticles"
                                                @fetchNextPage="fetchNextPage"
                                                @fetchPreviousPage="fetchPreviousPage"
                                                @fetchPage="fetchPage"
                                            />
                                        </div>
                                        <div v-else class="p-3 flex items-start gap-2">
                                            <XMarkIcon class="h-5 w-5 text-gray-400" />

                                            <p class="text-gray-600 text-sm font-medium">
                                                No articles found in this category.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </main>
                </div>
            </div>
        </div>
    </div>
</template>
