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
    import { ChatBubbleLeftRightIcon, HashtagIcon, PlusIcon } from '@heroicons/vue/24/outline';
    import { computed, ref } from 'vue';
    import ConversationListItem from './ConversationListItem.vue';
    import EmptyState from './ui/EmptyState.vue';

    const props = defineProps({
        conversations: { type: Array, required: true },
        selectedId: { type: String, default: null },
        unreadCounts: { type: Object, default: () => ({}) },
        loading: { type: Boolean, default: false },
        loadingMore: { type: Boolean, default: false },
        hasMore: { type: Boolean, default: false },
        currentUserId: { type: String, required: true },
    });

    const emit = defineEmits(['select', 'new-conversation', 'find-channels', 'pin', 'load-more']);

    const scrollContainer = ref(null);

    const pinnedConversations = computed(() => props.conversations.filter((conversation) => conversation.is_pinned));

    const unpinnedConversations = computed(() => props.conversations.filter((conversation) => !conversation.is_pinned));

    function handleScroll(event) {
        if (props.loadingMore || !props.hasMore) return;

        const container = event.target;
        const scrollBottom = container.scrollHeight - container.scrollTop - container.clientHeight;

        // Load more when within 100px of the bottom
        if (scrollBottom < 100) {
            emit('load-more');
        }
    }
</script>

<template>
    <div class="flex h-full flex-col bg-white dark:bg-gray-900">
        <!-- Header -->
        <div
            class="flex items-center justify-between bg-gradient-to-r from-primary-600 to-primary-700 dark:from-primary-700 dark:to-primary-800 px-6 py-4 shadow-md shrink-0 min-h-[75px]"
        >
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-white/20">
                    <ChatBubbleLeftRightIcon class="w-5 h-5 text-white" />
                </div>
                <h2 class="font-semibold text-white tracking-tight">Messages</h2>
            </div>
            <div class="flex items-center gap-1">
                <button
                    type="button"
                    class="text-white/90 hover:text-white hover:bg-white/10 transition-all rounded-lg p-1.5"
                    title="Find channels"
                    @click="emit('find-channels')"
                >
                    <HashtagIcon class="w-5 h-5" />
                </button>
                <button
                    type="button"
                    class="text-white/90 hover:text-white hover:bg-white/10 transition-all rounded-lg p-1.5"
                    title="New conversation"
                    @click="emit('new-conversation')"
                >
                    <PlusIcon class="w-5 h-5" />
                </button>
            </div>
        </div>

        <!-- Conversation List -->
        <div ref="scrollContainer" class="flex-1 overflow-y-auto" @scroll="handleScroll">
            <div v-if="loading" class="flex items-center justify-center p-8">
                <div class="flex items-center space-x-2">
                    <div class="w-2 h-2 bg-primary-400 rounded-full animate-bounce"></div>
                    <div class="w-2 h-2 bg-primary-400 rounded-full animate-bounce [animation-delay:0.2s]"></div>
                    <div class="w-2 h-2 bg-primary-400 rounded-full animate-bounce [animation-delay:0.4s]"></div>
                </div>
            </div>

            <EmptyState
                v-else-if="conversations.length === 0"
                :icon="ChatBubbleLeftRightIcon"
                message="No conversations yet"
                class="p-8"
            >
                <button
                    type="button"
                    class="mt-3 text-sm font-medium text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300"
                    @click="emit('new-conversation')"
                >
                    Start a new conversation
                </button>
            </EmptyState>

            <div v-else>
                <!-- Pinned Section -->
                <div v-if="pinnedConversations.length > 0">
                    <div
                        class="px-4 py-2 text-xs font-medium text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-800/50"
                    >
                        Pinned
                    </div>
                    <div class="divide-y divide-gray-100 dark:divide-gray-800">
                        <ConversationListItem
                            v-for="conversation in pinnedConversations"
                            :key="conversation.id"
                            :conversation="conversation"
                            :is-selected="selectedId === conversation.id"
                            :unread-count="unreadCounts[conversation.id] || 0"
                            :current-user-id="currentUserId"
                            @click="emit('select', conversation.id)"
                            @pin="emit('pin', $event)"
                        />
                    </div>
                </div>

                <!-- Unpinned Section -->
                <div v-if="unpinnedConversations.length > 0">
                    <div
                        v-if="pinnedConversations.length > 0"
                        class="px-4 py-2 text-xs font-medium text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-800/50"
                    >
                        All Messages
                    </div>
                    <div class="divide-y divide-gray-100 dark:divide-gray-800">
                        <ConversationListItem
                            v-for="conversation in unpinnedConversations"
                            :key="conversation.id"
                            :conversation="conversation"
                            :is-selected="selectedId === conversation.id"
                            :unread-count="unreadCounts[conversation.id] || 0"
                            :current-user-id="currentUserId"
                            @click="emit('select', conversation.id)"
                            @pin="emit('pin', $event)"
                        />
                    </div>
                </div>

                <!-- Loading More Indicator -->
                <div v-if="loadingMore" class="flex items-center justify-center py-4">
                    <div class="flex items-center space-x-2">
                        <div class="w-2 h-2 bg-primary-400 rounded-full animate-bounce"></div>
                        <div class="w-2 h-2 bg-primary-400 rounded-full animate-bounce [animation-delay:0.2s]"></div>
                        <div class="w-2 h-2 bg-primary-400 rounded-full animate-bounce [animation-delay:0.4s]"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
