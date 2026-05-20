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
    import { ChatBubbleLeftRightIcon, HashtagIcon, PlusIcon } from '@heroicons/vue/24/outline';
    import axios from 'axios';
    import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
    import ConversationListItem from './ConversationListItem.vue';
    import ServiceRequestConversationQueue from './ServiceRequestConversationQueue.vue';
    import EmptyState from './ui/EmptyState.vue';

    const props = defineProps({
        conversations: { type: Array, required: true },
        selectedId: { type: String, default: null },
        unreadCounts: { type: Object, default: () => ({}) },
        loading: { type: Boolean, default: false },
        loadingMore: { type: Boolean, default: false },
        hasMore: { type: Boolean, default: false },
        currentUserId: { type: String, required: true },
        initialTab: { type: String, default: 'users' },
        usersUnreadCount: { type: Number, default: 0 },
        contactsUnreadCount: { type: Number, default: 0 },
        serviceManagementEnabled: { type: Boolean, default: false },
    });

    const emit = defineEmits([
        'select',
        'new-conversation',
        'find-channels',
        'pin',
        'load-more',
        'queue-accepted',
        'tab-changed',
    ]);

    const scrollContainer = ref(null);
    const activeTab = ref(props.serviceManagementEnabled ? props.initialTab : 'users');
    const queueItems = ref([]);
    const queueLoading = ref(true);
    const now = ref(Date.now());
    let queueInterval = null;
    let tickInterval = null;

    const MAX_AGE_MS = 5 * 60 * 1000;

    const queueCount = computed(
        () =>
            queueItems.value.filter((item) => {
                const timestamp = /[Z+\-]\d{0,2}:?\d{0,2}$/.test(item.queued_at)
                    ? item.queued_at
                    : item.queued_at + 'Z';
                return now.value - new Date(timestamp).getTime() < MAX_AGE_MS;
            }).length,
    );

    const pinnedConversations = computed(() => props.conversations.filter((conversation) => conversation.is_pinned));

    const unpinnedConversations = computed(() => props.conversations.filter((conversation) => !conversation.is_pinned));

    async function fetchQueueCount() {
        try {
            const response = await axios.get('/api/chat/service-request-conversations/queue');
            queueItems.value = response.data.data;
        } catch {
            // silently fail
        } finally {
            queueLoading.value = false;
        }
    }

    onMounted(() => {
        fetchQueueCount();
        queueInterval = setInterval(fetchQueueCount, 15000);
        tickInterval = setInterval(() => {
            now.value = Date.now();
        }, 1000);
    });

    onUnmounted(() => {
        if (queueInterval) clearInterval(queueInterval);
        if (tickInterval) clearInterval(tickInterval);
    });

    function handleScroll(event) {
        if (props.loadingMore || !props.hasMore) return;

        const container = event.target;
        const scrollBottom = container.scrollHeight - container.scrollTop - container.clientHeight;

        if (scrollBottom < 100) {
            emit('load-more');
        }
    }

    function handleQueueAccepted(conversationId) {
        fetchQueueCount();
        emit('queue-accepted', conversationId);
    }

    watch(activeTab, (newTab) => {
        emit('tab-changed', newTab);
    });

    function addQueueItem(item) {
        const exists = queueItems.value.some((i) => i.id === item.id);
        if (!exists) {
            queueItems.value = [...queueItems.value, item];
        }
    }

    function focusContacts() {
        activeTab.value = 'contacts';
    }

    defineExpose({ focusContacts, addQueueItem });
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

        <!-- Tabs -->
        <div v-if="serviceManagementEnabled" class="px-3 pt-2 pb-1 shrink-0">
            <div class="flex gap-1 bg-gray-100 dark:bg-gray-800 p-1 rounded-lg">
                <button
                    type="button"
                    :class="[
                        'flex-1 text-sm font-medium py-2 rounded-md transition-all relative',
                        activeTab === 'users'
                            ? 'bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm'
                            : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300',
                    ]"
                    @click="activeTab = 'users'"
                >
                    Users
                    <span
                        v-if="usersUnreadCount > 0"
                        class="ml-1 inline-flex items-center justify-center min-w-5 h-5 px-1 text-xs font-bold text-white bg-primary-500 rounded-full"
                    >
                        {{ usersUnreadCount > 99 ? '99+' : usersUnreadCount }}
                    </span>
                </button>
                <button
                    type="button"
                    :class="[
                        'flex-1 text-sm font-medium py-2 rounded-md transition-all relative',
                        activeTab === 'contacts'
                            ? 'bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm'
                            : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300',
                    ]"
                    @click="activeTab = 'contacts'"
                >
                    Contacts
                    <span
                        v-if="contactsUnreadCount + queueCount > 0"
                        class="ml-1 inline-flex items-center justify-center min-w-5 h-5 px-1 text-xs font-bold text-white bg-primary-500 rounded-full"
                    >
                        {{ contactsUnreadCount + queueCount > 99 ? '99+' : contactsUnreadCount + queueCount }}
                    </span>
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

            <template v-else>
                <!-- Queue Section (Contacts tab only) -->
                <ServiceRequestConversationQueue
                    v-if="activeTab === 'contacts' && queueCount > 0"
                    :items="queueItems"
                    :loading="queueLoading"
                    :inline="true"
                    @accepted="handleQueueAccepted"
                    @refresh="fetchQueueCount"
                />

                <EmptyState
                    v-if="conversations.length === 0 && (activeTab !== 'contacts' || queueCount === 0)"
                    :icon="ChatBubbleLeftRightIcon"
                    message="No conversations yet"
                    class="p-8"
                >
                    <button
                        v-if="activeTab === 'users'"
                        type="button"
                        class="mt-3 text-sm font-medium text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300"
                        @click="emit('new-conversation')"
                    >
                        Start a new conversation
                    </button>
                </EmptyState>

                <div v-if="conversations.length > 0">
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
                            v-if="pinnedConversations.length > 0 || (activeTab === 'contacts' && queueCount > 0)"
                            class="px-4 py-2 text-xs font-medium text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-800/50"
                        >
                            {{ activeTab === 'contacts' ? 'Conversations' : 'All Messages' }}
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
                            <div
                                class="w-2 h-2 bg-primary-400 rounded-full animate-bounce [animation-delay:0.2s]"
                            ></div>
                            <div
                                class="w-2 h-2 bg-primary-400 rounded-full animate-bounce [animation-delay:0.4s]"
                            ></div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>
