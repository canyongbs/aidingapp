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
    import { HashtagIcon, MagnifyingGlassIcon, UserGroupIcon } from '@heroicons/vue/24/outline';
    import { useDebounceFn } from '@vueuse/core';
    import { onMounted, ref, watch } from 'vue';
    import { useConversations } from '../composables/useConversations';
    import BaseButton from './ui/BaseButton.vue';
    import BaseInput from './ui/BaseInput.vue';
    import BaseModal from './ui/BaseModal.vue';
    import ErrorAlert from './ui/ErrorAlert.vue';
    import LoadingSpinner from './ui/LoadingSpinner.vue';

    const props = defineProps({
        isOpen: { type: Boolean, default: false },
    });

    const emit = defineEmits(['close', 'joined']);

    const { fetchPublicChannels, joinChannel } = useConversations();

    const searchQuery = ref('');
    const channels = ref([]);
    const isLoading = ref(false);
    const joiningChannelId = ref(null);
    const error = ref('');

    const debouncedSearch = useDebounceFn(async (query) => {
        await loadChannels(query);
    }, 300);

    async function loadChannels(search = '') {
        isLoading.value = true;
        error.value = '';
        try {
            channels.value = await fetchPublicChannels(search);
        } catch {
            error.value = 'Failed to load channels';
            channels.value = [];
        } finally {
            isLoading.value = false;
        }
    }

    watch(searchQuery, (query) => {
        debouncedSearch(query);
    });

    watch(
        () => props.isOpen,
        (open) => {
            if (open) {
                searchQuery.value = '';
                error.value = '';
                loadChannels();
            }
        },
    );

    onMounted(() => {
        if (props.isOpen) {
            loadChannels();
        }
    });

    async function handleJoin(channel) {
        if (joiningChannelId.value) return;

        joiningChannelId.value = channel.id;
        error.value = '';

        try {
            const conversation = await joinChannel(channel.id);
            emit('joined', conversation);
        } catch (e) {
            error.value = e.response?.data?.message || 'Failed to join channel';
        } finally {
            joiningChannelId.value = null;
        }
    }

    function handleClose() {
        emit('close');
    }
</script>

<template>
    <BaseModal :is-open="isOpen" title="Find Channels" :max-height="true" :no-padding="true" @close="handleClose">
        <template #icon>
            <HashtagIcon class="w-5 h-5 text-primary-600 dark:text-primary-400" />
        </template>

        <!-- Search -->
        <div class="px-6 pt-4 pb-3 shrink-0">
            <BaseInput v-model="searchQuery" placeholder="Search channels...">
                <template #leading-icon>
                    <MagnifyingGlassIcon class="h-5 w-5 text-gray-400" />
                </template>
            </BaseInput>
        </div>

        <!-- Error Message -->
        <ErrorAlert v-if="error" :message="error" class="mx-6 mb-3" />

        <!-- Channel List -->
        <div class="flex-1 overflow-y-auto px-6 pb-6">
            <!-- Loading -->
            <div v-if="isLoading" class="flex justify-center py-8">
                <LoadingSpinner size="lg" />
            </div>

            <!-- Empty State -->
            <div v-else-if="channels.length === 0" class="py-8 text-center">
                <div
                    class="mx-auto w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-3"
                >
                    <HashtagIcon class="w-6 h-6 text-gray-400" />
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ searchQuery ? 'No channels found' : 'No public channels available' }}
                </p>
            </div>

            <!-- Channels -->
            <div v-else class="space-y-2">
                <div
                    v-for="channel in channels"
                    :key="channel.id"
                    class="flex items-center justify-between rounded-lg border border-gray-200 dark:border-gray-700 p-3 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
                >
                    <div class="flex items-center gap-3 min-w-0">
                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary-100 dark:bg-primary-900/30"
                        >
                            <HashtagIcon class="w-5 h-5 text-primary-600 dark:text-primary-400" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                {{ channel.name }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                <UserGroupIcon class="w-3.5 h-3.5" />
                                {{ channel.member_count }}
                                {{ channel.member_count === 1 ? 'member' : 'members' }}
                            </p>
                        </div>
                    </div>
                    <BaseButton
                        size="sm"
                        :loading="joiningChannelId === channel.id"
                        :disabled="joiningChannelId === channel.id"
                        @click="handleJoin(channel)"
                    >
                        {{ joiningChannelId === channel.id ? 'Joining...' : 'Join' }}
                    </BaseButton>
                </div>
            </div>
        </div>
    </BaseModal>
</template>
