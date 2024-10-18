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
<template>
    <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
        <div class="flex flex-1 justify-between sm:hidden">
            <button type="button"
                class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                :disabled="currentPage === 1" @click="$emit('fetchPreviousPage')">
                Previous
            </button>
            <button type="button"
                class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                :disabled="currentPage === lastPage" @click="$emit('fetchNextPage')">
                Next
            </button>
        </div>
        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-700">
                    Showing
                    <span class="font-medium">{{ fromArticle }}</span>
                    to
                    <span class="font-medium">{{ toArticle }}</span>
                    of
                    <span class="font-medium">{{ totalArticles }}</span>
                    results
                </p>
            </div>
            <!-- Pagination buttons -->
            <div>
                <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                    <button type="button"
                        class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0"
                        :disabled="currentPage === 1" @click="$emit('fetchPreviousPage')">
                        <span class="sr-only">Previous</span>
                        <ChevronLeftIcon class="h-5 w-5" aria-hidden="true" />
                    </button>

                    <!-- First Page Button -->
                    <button v-if="currentPage > 4"
                        class="relative z-10 inline-flex items-center px-4 py-2 text-sm font-semibold focus:z-20"
                        :class="currentPage === 1 ? 'bg-indigo-600 text-white' : 'bg-white-500 text-black border border-gray-300'"
                        @click="$emit('fetchPage', 1)">
                        1
                    </button>
                    <span v-if="currentPage > 4">...</span>

                    <!-- Page Numbers -->
                    <button v-for="page in visiblePages" :key="page" @click="$emit('fetchPage', page)"
                        class="relative z-10 inline-flex items-center px-4 py-2 text-sm font-semibold focus:z-20"
                        :class="page === currentPage ? 'bg-indigo-600 text-white' : 'bg-white-500 text-black border border-gray-300'"
                        :disabled="page === currentPage">
                        {{ page }}
                    </button>

                    <span v-if="currentPage < lastPage - 3">...</span>
                    <button v-if="currentPage < lastPage - 3"
                        class="relative z-10 inline-flex items-center px-4 py-2 text-sm font-semibold focus:z-20"
                        :class="currentPage === lastPage ? 'bg-indigo-600 text-white' : 'bg-white-500 text-black border border-gray-300'"
                        @click="$emit('fetchPage', lastPage)">
                        {{ lastPage }}
                    </button>

                    <button type="button"
                        class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0"
                        :disabled="currentPage === lastPage" @click="$emit('fetchNextPage')">
                        <span class="sr-only">Next</span>
                        <ChevronRightIcon class="h-5 w-5" aria-hidden="true" />
                    </button>
                </nav>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/20/solid';
import { defineProps, computed } from 'vue';

const props = defineProps({
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

const visiblePages = computed(() => {
    const range = 2;
    const start = Math.max(props.currentPage - range, 1);
    const end = Math.min(props.currentPage + range, props.lastPage);
    return Array.from({ length: end - start + 1 }, (_, i) => i + start);
});
</script>
