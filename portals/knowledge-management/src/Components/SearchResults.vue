<!--
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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
    import { ChevronRightIcon, XMarkIcon } from '@heroicons/vue/20/solid';
    import { defineProps } from 'vue';
    import Article from './Article.vue';
    import FilterComponent from './FilterComponent.vue';
    import Pagination from './Pagination.vue';
    import SearchLoading from './SearchLoading.vue';

    const emit = defineEmits(['fetchNextPage', 'fetchPreviousPage', 'fetchPage', 'change-filter']);

    defineProps({
        searchQuery: {
            type: String,
            required: true,
        },
        searchResults: {
            type: Object,
            required: true,
        },
        loadingResults: {
            type: Boolean,
            required: true,
        },
        selectedFilter: {
            type: String,
            default: '',
        },
        currentPage: {
            type: Number,
            required: true,
        },
        lastPage: {
            type: Number,
            required: true,
        },
        fromArticle: {
            type: Number,
            required: true,
        },
        toArticle: {
            type: Number,
            required: true,
        },
        totalArticles: {
            type: Number,
            required: true,
        },
    });

    const updateFilter = (value) => {
        emit('change-filter', value);
    };

    function fetchNextPage() {
        emit('fetchNextPage');
    }
    function fetchPreviousPage() {
        emit('fetchPreviousPage');
    }
    function fetchPage(page) {
        emit('fetchPage', page);
    }
</script>

<template>
    <div v-if="loadingResults">
        <SearchLoading />
    </div>

    <div v-if="!loadingResults && searchResults?.data" class="flex flex-col gap-6">
        <h3 class="text-2xl font-bold text-brand-950">
            Search results: <span class="font-normal">{{ searchQuery }}</span>
        </h3>

        <filter-component @change-filter="updateFilter" :selected-filter="selectedFilter"></filter-component>
        <div class="flex flex-col divide-y ring-1 ring-black/5 shadow-sm px-3 pt-3 pb-1 rounded bg-white">
            <h4 class="text-lg font-semibold text-gray-800 px-3 pt-1 pb-3">Articles ({{ totalArticles }})</h4>

            <div v-if="searchResults.data.articles.data.length > 0">
                <ul role="list" class="divide-y">
                    <li v-for="article in searchResults.data.articles.data" :key="article.id">
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

                <p class="text-gray-600 text-sm font-medium">No articles found that match this search.</p>
            </div>
        </div>

        <div class="flex flex-col divide-y ring-1 ring-black/5 shadow-sm px-3 pt-3 pb-1 rounded bg-white">
            <h4 class="text-lg font-semibold text-gray-800 px-3 pt-1 pb-3">Categories</h4>

            <div v-if="searchResults.data.categories.length > 0">
                <ul role="list" class="divide-y">
                    <li v-for="category in searchResults.data.categories" :key="category.slug">
                        <router-link
                            :to="{ name: 'view-category', params: { categorySlug: category.slug } }"
                            class="group p-3 flex items-start text-sm font-medium text-gray-700"
                        >
                            <h5>
                                {{ category.name }}
                            </h5>

                            <ChevronRightIcon
                                class="opacity-0 h-5 w-5 text-brand-600 transition-all group-hover:translate-x-2 group-hover:opacity-100"
                            />
                        </router-link>
                    </li>
                </ul>
            </div>
            <div v-else class="p-3 flex items-start gap-2">
                <XMarkIcon class="h-5 w-5 text-gray-400" />

                <p class="text-gray-600 text-sm font-medium">No categories found that match this search.</p>
            </div>
        </div>
    </div>
</template>
