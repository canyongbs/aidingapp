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
    import { ChevronRightIcon } from '@heroicons/vue/20/solid';
    import { DocumentTextIcon, FolderIcon } from '@heroicons/vue/24/outline';
    import { defineProps } from 'vue';
    import Article from './Article.vue';
    import EmptyState from './EmptyState.vue';
    import Pagination from './Pagination.vue';
    import SearchLoading from './SearchLoading.vue';
    import Subheading from './Subheading.vue';
    import Tabs from './Tabs.vue';

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

    const filterTabs = [
        { label: 'All Articles', value: 'all-articles' },
        { label: 'Featured', value: 'featured' },
        { label: 'Most Viewed', value: 'most-viewed' },
    ];

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
        <Subheading>
            Search results for <span class="text-gray-500">&ldquo;{{ searchQuery }}&rdquo;</span>
        </Subheading>

        <div class="flex flex-col rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
            <Tabs :tabs="filterTabs" :modelValue="selectedFilter || 'all-articles'" @update:modelValue="updateFilter" :contained="true" />

            <div v-if="searchResults.data.articles.data.length > 0" class="divide-y">
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
            <EmptyState v-else :contained="false" :icon="DocumentTextIcon">
                <template #heading>No articles found</template>
                <template #description>No articles match your current search criteria.</template>
            </EmptyState>
        </div>

        <div class="flex flex-col rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5">
            <div v-if="searchResults.data.categories.length > 0" class="divide-y">
                <ul role="list" class="divide-y">
                    <li v-for="category in searchResults.data.categories" :key="category.slug">
                        <router-link
                            :to="{ name: 'view-category', params: { categorySlug: category.slug } }"
                            class="group flex items-center px-6 py-3 text-sm font-medium text-gray-700 transition duration-75 hover:bg-gray-50"
                        >
                            <span class="flex-1">{{ category.name }}</span>

                            <ChevronRightIcon
                                class="size-5 text-gray-400 opacity-0 transition-all group-hover:translate-x-1 group-hover:opacity-100"
                            />
                        </router-link>
                    </li>
                </ul>
            </div>
            <EmptyState v-else :contained="false" :icon="FolderIcon">
                <template #heading>No categories found</template>
                <template #description>No categories match your current search criteria.</template>
            </EmptyState>
        </div>
    </div>
</template>
