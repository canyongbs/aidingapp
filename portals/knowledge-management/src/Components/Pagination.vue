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
    import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/20/solid';
    import { computed, defineProps } from 'vue';
    import BaseButton from './ui/BaseButton.vue';

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

<template>
    <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
        <div class="flex flex-1 justify-between sm:hidden">
            <BaseButton variant="neutral" size="md" :disabled="currentPage === 1" @click="$emit('fetchPreviousPage')">
                Previous
            </BaseButton>
            <BaseButton
                variant="neutral"
                size="md"
                :disabled="currentPage === lastPage"
                @click="$emit('fetchNextPage')"
            >
                Next
            </BaseButton>
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
                <nav class="isolate inline-flex -space-x-px rounded-md shadow-xs" aria-label="Pagination">
                    <BaseButton
                        variant="ghost"
                        size="md"
                        icon-only
                        :icon-left="ChevronLeftIcon"
                        :disabled="currentPage === 1"
                        class="!rounded-r-none"
                        aria-label="Previous page"
                        @click="$emit('fetchPreviousPage')"
                    />

                    <!-- First Page Button -->
                    <BaseButton
                        v-if="currentPage > 4"
                        variant="ghost"
                        size="md"
                        class="!rounded-none"
                        @click="$emit('fetchPage', 1)"
                    >
                        1
                    </BaseButton>
                    <span v-if="currentPage > 4" class="inline-flex items-center px-2 text-gray-500 text-sm">
                        &hellip;
                    </span>

                    <!-- Page Numbers -->
                    <BaseButton
                        v-for="page in visiblePages"
                        :key="page"
                        :variant="page === currentPage ? 'primary' : 'ghost'"
                        :selected="page === currentPage"
                        size="md"
                        class="!rounded-none"
                        @click="$emit('fetchPage', page)"
                    >
                        {{ page }}
                    </BaseButton>

                    <span v-if="currentPage < lastPage - 3" class="inline-flex items-center px-2 text-gray-500 text-sm">
                        &hellip;
                    </span>
                    <BaseButton
                        v-if="currentPage < lastPage - 3"
                        variant="ghost"
                        size="md"
                        class="!rounded-none"
                        @click="$emit('fetchPage', lastPage)"
                    >
                        {{ lastPage }}
                    </BaseButton>
                    <BaseButton
                        variant="ghost"
                        size="md"
                        icon-only
                        :icon-left="ChevronRightIcon"
                        :disabled="currentPage === lastPage"
                        class="!rounded-l-none"
                        aria-label="Next page"
                        @click="$emit('fetchNextPage')"
                    />
                </nav>
            </div>
        </div>
    </div>
</template>
