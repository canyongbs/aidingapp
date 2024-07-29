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
    import { MagnifyingGlassIcon } from '@heroicons/vue/24/outline';
    import { defineProps, ref, watch, onMounted } from 'vue';
    import { useRoute } from 'vue-router';
    import Breadcrumbs from '../Components/Breadcrumbs.vue';
    import AppLoading from '../Components/AppLoading.vue';
    import { consumer } from '../Services/Consumer.js';
    import { Bars3Icon } from '@heroicons/vue/24/outline/index.js';
    import { ChevronRightIcon, XMarkIcon } from '@heroicons/vue/20/solid/index.js';
    import Tags from '../Components/Tags.vue';
    import Article from '../Components/Article.vue';
    import SearchResults from '../Components/SearchResults.vue';
    import Badge from '../Components/Badge.vue';

    const route = useRoute();

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

    const debounceSearch = debounce((value) => {
        const { post } = consumer();

        if (!value && selectedTags.value.length < 1) {
            searchQuery.value = null;
            searchResults.value = null;
            return;
        }

        loadingeSearchResults.value = true;

        post(props.searchUrl, {
            search: JSON.stringify(value),
            tags: selectedTags.value.join(','),
        }).then((response) => {
            searchResults.value = response.data;
            loadingeSearchResults.value = false;
        });
    }, 500);

    watch(searchQuery, (value) => {
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

    watch(
        route,
        function (newRouteValue) {
            getData();
        },
        {
            immediate: true,
        },
    );

    onMounted(function () {
        getData();
    });

    function getData() {
        loadingResults.value = true;

        const { get } = consumer();

        get(props.apiUrl + '/categories/' + route.params.categoryId).then((response) => {
            category.value = response.data.category;
            articles.value = response.data.articles;
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

                <form action="#" method="GET">
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
                </form>
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
                        <Breadcrumbs :currentCrumb="category.name"></Breadcrumbs>
                        <div v-if="searchQuery || selectedTags.length > 0" class="flex flex-col gap-6">
                            <SearchResults
                                :searchQuery="searchQuery"
                                :searchResults="searchResults"
                                :loadingResults="loadingeSearchResults"
                            >
                            </SearchResults>
                        </div>
                        <div v-else class="flex flex-col gap-6">
                            <h2 class="text-2xl font-bold text-primary-950">
                                {{ category.name }}
                            </h2>

                            <div>
                                <div
                                    class="flex flex-col divide-y ring-1 ring-black/5 shadow-sm px-3 pt-3 pb-1 rounded bg-white"
                                >
                                    <h3 class="text-lg font-semibold text-gray-800 px-3 pt-1 pb-3">Articles</h3>

                                    <div v-if="articles.length > 0">
                                        <ul role="list" class="divide-y">
                                            <li v-for="article in articles" :key="article.id">
                                                <Article :article="article" />
                                            </li>
                                        </ul>
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
                    </main>
                </div>
            </div>
        </div>
    </div>
</template>
