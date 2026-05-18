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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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
    import { CheckIcon, ClockIcon, XMarkIcon } from '@heroicons/vue/24/outline';
    import axios from 'axios';
    import { computed, onMounted, onUnmounted, ref } from 'vue';

    const props = defineProps({
        items: { type: Array, required: true },
        loading: { type: Boolean, default: false },
    });

    const emit = defineEmits(['accepted', 'refresh']);

    const now = ref(Date.now());
    let tickInterval = null;

    onMounted(() => {
        tickInterval = setInterval(() => {
            now.value = Date.now();
        }, 1000);
    });

    onUnmounted(() => {
        if (tickInterval) clearInterval(tickInterval);
    });

    const MAX_AGE_MS = 5 * 60 * 1000;

    const activeItems = computed(() =>
        props.items.filter((item) => {
            const timestamp = /[Z+\-]\d{0,2}:?\d{0,2}$/.test(item.queued_at) ? item.queued_at : item.queued_at + 'Z';
            return now.value - new Date(timestamp).getTime() < MAX_AGE_MS;
        }),
    );

    async function accept(item) {
        try {
            const response = await axios.post(`/api/chat/service-request-queue/${item.id}/accept`);
            emit('accepted', response.data.conversation_id);
        } catch {
            emit('refresh');
        }
    }

    async function decline(item) {
        try {
            await axios.post(`/api/chat/service-request-queue/${item.id}/decline`);
            emit('refresh');
        } catch {
            emit('refresh');
        }
    }

    function timeElapsed(queuedAt) {
        const timestamp = /[Z+\-]\d{0,2}:?\d{0,2}$/.test(queuedAt) ? queuedAt : queuedAt + 'Z';
        const seconds = Math.max(0, Math.floor((now.value - new Date(timestamp).getTime()) / 1000));
        const mins = Math.floor(seconds / 60);
        const secs = seconds % 60;
        return `${mins}:${secs.toString().padStart(2, '0')}`;
    }
</script>

<template>
    <div class="flex-1 overflow-y-auto">
        <template v-if="loading">
            <div class="flex items-center justify-center p-8">
                <div class="flex items-center space-x-2">
                    <div class="w-2 h-2 bg-primary-400 rounded-full animate-bounce"></div>
                    <div class="w-2 h-2 bg-primary-400 rounded-full animate-bounce [animation-delay:0.2s]"></div>
                    <div class="w-2 h-2 bg-primary-400 rounded-full animate-bounce [animation-delay:0.4s]"></div>
                </div>
            </div>
        </template>

        <template v-else-if="activeItems.length === 0">
            <div class="flex flex-col items-center justify-center p-8 text-center">
                <ClockIcon class="w-10 h-10 text-gray-300 dark:text-gray-600 mb-2" />
                <p class="text-sm text-gray-500 dark:text-gray-400">No pending chat requests</p>
            </div>
        </template>

        <template v-else>
            <div class="divide-y divide-gray-100 dark:divide-gray-800">
                <div v-for="item in activeItems" :key="item.id" class="px-4 py-3">
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                {{ item.contact_name }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                {{ item.service_request_title }}
                            </p>
                        </div>
                        <span class="text-xs text-gray-400 dark:text-gray-500 tabular-nums whitespace-nowrap">
                            {{ timeElapsed(item.queued_at) }}
                        </span>
                    </div>

                    <div class="flex items-center gap-2">
                        <button
                            @click="accept(item)"
                            class="flex-1 flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-md bg-primary-600 hover:bg-primary-700 text-white text-xs font-medium transition-colors"
                        >
                            <CheckIcon class="w-3.5 h-3.5" />
                            Accept
                        </button>
                        <button
                            @click="decline(item)"
                            class="flex-1 flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-md bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-xs font-medium transition-colors"
                        >
                            <XMarkIcon class="w-3.5 h-3.5" />
                            Decline
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>
