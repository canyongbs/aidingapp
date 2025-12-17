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
    import { ArrowLeftIcon, ChatBubbleLeftEllipsisIcon } from '@heroicons/vue/24/outline';
    import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
    import ConversationHeader from './components/ConversationHeader.vue';
    import ConversationList from './components/ConversationList.vue';
    import FindChannelsModal from './components/FindChannelsModal.vue';
    import MessageInput from './components/MessageInput.vue';
    import MessageList from './components/MessageList.vue';
    import NewConversationModal from './components/NewConversationModal.vue';
    import ParticipantList from './components/ParticipantList.vue';
    import LoadingSpinner from './components/ui/LoadingSpinner.vue';
    import { useConversations } from './composables/useConversations';
    import { useMessages } from './composables/useMessages';
    import { useTypingIndicator } from './composables/useTypingIndicator';
    import { useWebSocket } from './composables/useWebSocket';
    import { useChatStore } from './stores/chat';

    const props = defineProps({
        userId: { type: String, required: true },
        userName: { type: String, required: true },
        userAvatar: { type: String, default: null },
    });

    const store = useChatStore();
    const {
        conversations,
        loading: conversationsLoading,
        hasMore: conversationsHasMore,
        loadConversations,
        markAsRead,
        updateSettings,
        updateConversation,
        togglePin,
        fetchConversation,
    } = useConversations();

    const loadingMoreConversations = ref(false);
    const {
        subscribeToUserChannel,
        subscribeToAllConversations,
        subscribeToConversation,
        joinPresence,
        leavePresence,
        disconnect,
    } = useWebSocket();

    const selectedConversationId = computed(() => store.selectedConversationId);
    const selectedConversation = computed(() => store.selectedConversation);

    const {
        messages,
        loading: messagesLoading,
        hasMore,
        loadMessages,
        sendMessage,
        retryMessage,
        removeFailedMessage,
    } = useMessages(selectedConversationId);
    const { typingUsers, onTyping } = useTypingIndicator(selectedConversationId);

    const showNewConversationModal = ref(false);
    const showFindChannelsModal = ref(false);
    const showParticipants = ref(false);

    function handlePageClose() {
        disconnect();
    }

    async function handleVisibilityChange() {
        if (document.hidden) {
            // Leave presence when tab is hidden (but stay subscribed for messages)
            leavePresence();
        } else if (selectedConversationId.value) {
            // Rejoin presence when tab becomes visible again
            joinPresence(selectedConversationId.value);
            await markAsRead(selectedConversationId.value);
        }
    }

    onMounted(async () => {
        store.setCurrentUser({
            id: props.userId,
            name: props.userName,
            avatar: props.userAvatar,
        });

        subscribeToUserChannel(props.userId, {
            onUnknownConversation: async (conversationId) => {
                await fetchConversation(conversationId);
                subscribeToConversation(conversationId);
            },
        });

        const loaded = await loadConversations();
        subscribeToAllConversations(loaded);

        // Check for conversation query parameter and auto-select
        const urlParams = new URLSearchParams(window.location.search);
        const conversationId = urlParams.get('conversation');
        if (conversationId) {
            const conversation = loaded.find((loadedConversation) => loadedConversation.id === conversationId);
            if (conversation) {
                store.selectConversation(conversationId);
            }
        }

        // Handle browser tab close/navigation
        window.addEventListener('beforeunload', handlePageClose);
        window.addEventListener('pagehide', handlePageClose);
        document.addEventListener('visibilitychange', handleVisibilityChange);
    });

    onUnmounted(() => {
        window.removeEventListener('beforeunload', handlePageClose);
        window.removeEventListener('pagehide', handlePageClose);
        document.removeEventListener('visibilitychange', handleVisibilityChange);
        disconnect();
    });

    watch(selectedConversationId, async (newId) => {
        // Update URL with conversation ID
        const url = new URL(window.location.href);
        if (newId) {
            url.searchParams.set('conversation', newId);
            joinPresence(newId);
            await fetchConversation(newId);
            await loadMessages();
            await markAsRead(newId);
        } else {
            url.searchParams.delete('conversation');
            joinPresence(null);
        }
        window.history.replaceState({}, '', url);
    });

    function handleSelectConversation(conversationId) {
        store.selectConversation(conversationId);
        showParticipants.value = false;
    }

    function handleNewConversation() {
        showNewConversationModal.value = true;
    }

    function handleFindChannels() {
        showFindChannelsModal.value = true;
    }

    function handleChannelJoined(conversation) {
        showFindChannelsModal.value = false;
        store.selectConversation(conversation.id);
        subscribeToConversation(conversation.id);
    }

    function handleConversationCreated(conversation) {
        showNewConversationModal.value = false;
        store.addConversation(conversation);
        store.selectConversation(conversation.id);
        subscribeToConversation(conversation.id);
    }

    async function handleSendMessage(content) {
        await sendMessage(content);
    }

    function handleLoadMore() {
        if (hasMore.value && !messagesLoading.value) {
            loadMessages(true);
        }
    }

    function handleToggleParticipants() {
        showParticipants.value = !showParticipants.value;
    }

    async function handleUpdateSettings(settings) {
        if (!selectedConversationId.value) return;

        const updated = await updateSettings(selectedConversationId.value, settings);
        store.updateConversation(selectedConversationId.value, updated);
    }

    async function handleUpdateConversation(updates) {
        if (!selectedConversationId.value) return;

        await updateConversation(selectedConversationId.value, updates);
    }

    function handleBackToList() {
        store.selectConversation(null);
        showParticipants.value = false;
    }

    async function handleTogglePin(conversationId) {
        await togglePin(conversationId);
    }

    async function handleLoadMoreConversations() {
        if (loadingMoreConversations.value || !conversationsHasMore.value) return;

        loadingMoreConversations.value = true;
        try {
            const loaded = await loadConversations(true);
            subscribeToAllConversations(loaded);
        } finally {
            loadingMoreConversations.value = false;
        }
    }

    async function handleParticipantsUpdated() {
        if (selectedConversationId.value) {
            await fetchConversation(selectedConversationId.value);
        }
    }

    async function handleRetryMessage(messageId) {
        try {
            await retryMessage(messageId);
        } catch {
            // Error is already handled by marking message as failed
        }
    }

    function handleDismissMessage(messageId) {
        removeFailedMessage(messageId);
    }
</script>

<template>
    <div class="relative flex h-full overflow-hidden rounded-xl shadow-2xl ring-1 ring-gray-950/5 dark:ring-white/10">
        <!-- Conversation List (Left Sidebar) -->
        <!-- On mobile: hidden when conversation is selected -->
        <div
            class="w-full md:w-80 flex-shrink-0 border-r border-gray-200 dark:border-gray-700"
            :class="[selectedConversationId ? 'hidden md:block' : 'block']"
        >
            <ConversationList
                :conversations="conversations"
                :selected-id="selectedConversationId"
                :unread-counts="store.unreadCounts"
                :loading="conversationsLoading"
                :loading-more="loadingMoreConversations"
                :has-more="conversationsHasMore"
                :current-user-id="userId"
                @select="handleSelectConversation"
                @new-conversation="handleNewConversation"
                @find-channels="handleFindChannels"
                @pin="handleTogglePin"
                @load-more="handleLoadMoreConversations"
            />
        </div>

        <!-- Main Chat Area -->
        <!-- On mobile: hidden when no conversation is selected -->
        <div
            class="flex-1 flex-col bg-white dark:bg-gray-900"
            :class="[selectedConversationId ? 'flex' : 'hidden md:flex']"
        >
            <template v-if="selectedConversation">
                <ConversationHeader
                    :conversation="selectedConversation"
                    :current-user-id="userId"
                    @show-participants="handleToggleParticipants"
                    @update-settings="handleUpdateSettings"
                    @update-conversation="handleUpdateConversation"
                >
                    <!-- Mobile back button -->
                    <template #prepend>
                        <button
                            type="button"
                            class="md:hidden text-white/90 hover:text-white hover:bg-white/10 transition-all rounded-lg p-1.5"
                            @click="handleBackToList"
                        >
                            <ArrowLeftIcon class="w-5 h-5" />
                        </button>
                    </template>
                </ConversationHeader>

                <MessageList
                    :messages="messages"
                    :loading="messagesLoading"
                    :has-more="hasMore"
                    :current-user-id="userId"
                    :typing-users="typingUsers"
                    :conversation="selectedConversation"
                    @load-more="handleLoadMore"
                    @retry="handleRetryMessage"
                    @dismiss="handleDismissMessage"
                />

                <MessageInput
                    :disabled="false"
                    :participants="selectedConversation.participants || []"
                    :current-user-id="userId"
                    @send="handleSendMessage"
                    @typing="onTyping"
                />
            </template>

            <template v-else-if="conversationsLoading">
                <div
                    class="flex flex-1 items-center justify-center bg-gradient-to-b from-gray-50 to-white dark:from-gray-800 dark:to-gray-900"
                >
                    <LoadingSpinner label="Loading..." />
                </div>
            </template>

            <template v-else>
                <div
                    class="flex flex-1 items-center justify-center bg-gradient-to-b from-gray-50 to-white dark:from-gray-800 dark:to-gray-900"
                >
                    <div class="text-center">
                        <div
                            class="mx-auto w-16 h-16 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center mb-4"
                        >
                            <ChatBubbleLeftEllipsisIcon class="w-8 h-8 text-primary-600 dark:text-primary-400" />
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">No conversation selected</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Select a conversation to start chatting</p>
                    </div>
                </div>
            </template>
        </div>

        <!-- Participants Sidebar (for channels) -->
        <!-- Mobile: full overlay, Desktop: sliding sidebar -->
        <div
            v-if="selectedConversation?.type === 'channel'"
            class="hidden md:block overflow-hidden flex-shrink-0 border-l border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 transition-[width] duration-200 ease-out"
            :style="{ width: showParticipants ? '16rem' : '0' }"
        >
            <div class="w-64">
                <ParticipantList
                    :conversation="selectedConversation"
                    :current-user-id="userId"
                    @participants-updated="handleParticipantsUpdated"
                />
            </div>
        </div>

        <!-- Mobile participants overlay -->
        <transition
            enter-active-class="transition-opacity duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-200 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="showParticipants && selectedConversation?.type === 'channel'"
                class="md:hidden absolute inset-0 bg-white dark:bg-gray-900 z-10"
            >
                <ParticipantList
                    :conversation="selectedConversation"
                    :current-user-id="userId"
                    :show-back-button="true"
                    @back="showParticipants = false"
                    @participants-updated="handleParticipantsUpdated"
                />
            </div>
        </transition>

        <!-- New Conversation Modal -->
        <NewConversationModal
            :is-open="showNewConversationModal"
            :current-user-id="userId"
            @close="showNewConversationModal = false"
            @created="handleConversationCreated"
        />

        <!-- Find Channels Modal -->
        <FindChannelsModal
            :is-open="showFindChannelsModal"
            @close="showFindChannelsModal = false"
            @joined="handleChannelJoined"
        />
    </div>
</template>
