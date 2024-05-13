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
    import { Bars3Icon, MagnifyingGlassIcon } from '@heroicons/vue/24/outline';
    import HelpCenter from '../Components/HelpCenter.vue';
    import SearchResults from '../Components/SearchResults.vue';
    import { defineProps, ref, watch } from 'vue';
    import { consumer } from '../Services/Consumer.js';
    import { useAuthStore } from '../Stores/auth.js';
    import { useFeatureStore } from '../Stores/feature.js';
    import { Listbox, ListboxButton, ListboxOption, ListboxOptions } from "@headlessui/vue";

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
        serviceRequests: {
            type: Object,
            required: true,
        },
        tags: {
            type: Object,
            required: true,
        },
    });

    const searchQuery = ref('');
    const loadingResults = ref(false);
    const searchResults = ref(null);
    const selectedTags = ref([]);

    const debounceSearch = debounce((value) => {
        const { post } = consumer();

        if (!value && selectedTags.value.length < 1) {
            searchQuery.value = null;
            searchResults.value = null;
            return;
        }

        loadingResults.value = true;

        post(props.searchUrl, {
            search: JSON.stringify(value),
            tags: selectedTags.value.join(','),
        }).then((response) => {
            searchResults.value = response.data;
            loadingResults.value = false;
        });
    }, 500);

    const { user } = useAuthStore();
    const { hasServiceManagement } = useFeatureStore();

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
</script>

<template>
    <div class="sticky top-0 z-40 flex flex-col items-center bg-gray-50">
        <button class="w-full p-3 lg:hidden" type="button" v-on:click="showMobileMenu = !showMobileMenu">
            <span class="sr-only">Open sidebar</span>

            <Bars3Icon class="h-6 w-6 text-gray-900"></Bars3Icon>
        </button>

        <div class="bg-gradient-to-br from-primary-500 to-primary-800 w-full px-6">
            <div class="max-w-screen-xl flex flex-col gap-y-6 mx-auto py-8">
                <div class="text-right" v-if="hasServiceManagement && user">
                    <router-link :to="{ name: 'create-service-request' }">
                        <button class="p-2 font-bold rounded bg-white text-primary-700 dark:text-primary-400">
                            New Request
                        </button>
                    </router-link>
                </div>

                <div class="flex flex-col gap-y-1 text-left">
                    <h3 class="text-3xl font-semibold text-white">Need help?</h3>
                    <p class="text-primary-100">Search our knowledge base for advice and answers</p>
                </div>

                <form action="#" method="GET">
                    <label for="search" class="sr-only">Search</label>
<!--                    <div class="flex relative rounded">-->
<!--                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">-->
<!--                            <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />-->
<!--                        </div>-->

<!--                        <input-->
<!--                            type="search"-->
<!--                            v-model="searchQuery"-->
<!--                            id="search"-->
<!--                            placeholder="Search for articles and categories"-->
<!--                            class="block w-full rounded rounded-r-none border-0 py-3 pl-12 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-2&#45;&#45; sm:text-sm sm:leading-6"-->
<!--                        />-->

<!--                        <select id="tags" class="block rounded rounded-l-none border-0 py-3 pl-12 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-2&#45;&#45; sm:text-sm sm:leading-6">-->
<!--                            -->
<!--                        </select>-->
<!--                    </div>-->
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
                                class="block w-full rounded rounded-b-none border-0 pl-12 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-2-- sm:text-sm sm:leading-6"
                            />
<!--                        <div class="absolute inset-y-0 right-0 flex items-center">-->
<!--                            <select-->
<!--                                id="tags"-->
<!--                                class="h-full rounded-md border-0 bg-transparent py-0 pl-2 pr-7 text-gray-500 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm"-->
<!--                                multiple-->
<!--                            >-->
<!--                                <option v-for="tag in tags" :key="tag.id">{{ tag.name }}</option>-->
<!--                            </select>-->
<!--&lt;!&ndash;                            <div class="h-full rounded rounded-l-none border-0 bg-transparent pl-2 pr-7 text-gray-900 focus:ring-2 focus:ring-inset focus:ring-primary-2&#45;&#45; sm:text-sm">&ndash;&gt;-->
<!--&lt;!&ndash;                                <Listbox&ndash;&gt;-->
<!--&lt;!&ndash;                                    v-model="selectedTags"&ndash;&gt;-->
<!--&lt;!&ndash;                                    multiple&ndash;&gt;-->
<!--&lt;!&ndash;                                    as="div"&ndash;&gt;-->
<!--&lt;!&ndash;                                >&ndash;&gt;-->
<!--&lt;!&ndash;                                    <ListboxButton>&ndash;&gt;-->
<!--&lt;!&ndash;                                        <span v-if="selectedTags.length < 1">Tags</span>&ndash;&gt;-->
<!--&lt;!&ndash;                                        <span v-else-if="selectedTags.length === 1">{{ selectedTags[0].name }}</span>&ndash;&gt;-->
<!--&lt;!&ndash;                                        <span v-else>{{ selectedTags.length }} tags selected</span>&ndash;&gt;-->
<!--&lt;!&ndash;                                    </ListboxButton>&ndash;&gt;-->
<!--&lt;!&ndash;                                    <ListboxOptions>&ndash;&gt;-->
<!--&lt;!&ndash;                                        <ListboxOption v-for="tag in tags" :key="tag.id" :value="tag.id">&ndash;&gt;-->
<!--&lt;!&ndash;                                            {{ tag.name }}&ndash;&gt;-->
<!--&lt;!&ndash;                                        </ListboxOption>&ndash;&gt;-->
<!--&lt;!&ndash;                                    </ListboxOptions>&ndash;&gt;-->
<!--&lt;!&ndash;                                </Listbox>&ndash;&gt;-->
<!--&lt;!&ndash;                            </div>&ndash;&gt;-->
<!--                        </div>-->
                        </div>
                    </div>
<!--                    <select-->
<!--                        id="tags"-->
<!--                        class="w-full rounded rounded-t-none border-0 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-2&#45;&#45; sm:text-sm sm:leading-6"-->
<!--                        multiple-->
<!--                    >-->
<!--                        <option v-for="tag in tags" :key="tag.id">{{ tag.name }}</option>-->
<!--                    </select>-->
<!--                    <div-->
<!--                        id="tags"-->
<!--                        class="py-3 w-full rounded rounded-t-none border-0 bg-white text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-2&#45;&#45; sm:text-sm sm:leading-6"-->

<!--                    >-->
<!--&lt;!&ndash;                        <option v-for="tag in tags" :key="tag.id">{{ tag.name }}</option>&ndash;&gt;-->
<!--                        <label>Tags</label>-->
<!--                        <ul>-->
<!--                            <li v-for="tag in tags" :key="tag.id">-->
<!--                                <input type="checkbox" :value="tag.id" v-model="selectedTags" />-->
<!--                                <label>{{ tag.name }}</label>-->
<!--                            </li>-->
<!--                        </ul>-->
<!--                    </div>-->
                    <details class="rounded rounded-t-none bg-white py-3 pl-4 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-primary-2-- sm:text-sm sm:leading-6">
                        <summary v-if="selectedTags.length > 0">Tags ({{ selectedTags.length }} selected)</summary>
                        <summary v-else>Tags</summary>
                        <fieldset>
                            <ul>
                                <li v-for="tag in tags" :key="tag.id">
                                    <input type="checkbox" :value="tag.id" @click="toggleTag(tag.id)"/>
                                    <label class="pl-4">{{ tag.name }}</label>
                                </li>
                            </ul>
                        </fieldset>
                    </details>
                </form>
            </div>
        </div>
    </div>

    <main class="px-6">
        <div class="max-w-screen-xl flex flex-col gap-y-6 mx-auto py-8">
            <SearchResults
                v-if="searchQuery || selectedTags.length > 0"
                :searchQuery="searchQuery"
                :searchResults="searchResults"
                :loadingResults="loadingResults"
            >
            </SearchResults>

            <HelpCenter v-else :categories="categories" :service-requests="serviceRequests"></HelpCenter>
        </div>
    </main>
</template>
