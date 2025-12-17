/*
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
*/

import { computed, ref } from 'vue';
import api from '../services/api';
import { useChatStore } from '../stores/chat';

export function useConversations() {
    const store = useChatStore();
    const markAsReadTimeout = ref(null);
    const pendingMarkAsRead = ref(null);

    const conversations = computed(() => store.conversations);
    const loading = computed(() => store.conversationsLoading);
    const hasMore = computed(() => store.conversationsHasMore);

    async function loadConversations(loadMore = false) {
        if (store.conversationsLoading) return store.conversations;

        if (!loadMore) {
            store.conversationsLoading = true;
        }
        try {
            const params = {};
            if (loadMore && store.conversationsNextCursor) {
                params.cursor = store.conversationsNextCursor;
            }

            const { data } = await api.get('/conversations', { params });

            if (loadMore) {
                // Only append non-pinned conversations when loading more
                store.appendConversations(data.data);
            } else {
                // On initial load, combine pinned (all) + non-pinned (first page)
                const pinned = data.pinned || [];
                const allConversations = [...pinned, ...data.data];
                store.setConversations(allConversations);
            }

            store.setConversationsPagination(data.next_cursor, data.has_more);

            // Populate unread counts from loaded conversations
            const counts = loadMore ? { ...store.unreadCounts } : {};

            // Count from pinned (only on initial load)
            if (!loadMore && data.pinned) {
                data.pinned.forEach((conversation) => {
                    if (conversation.unread_count !== undefined) {
                        counts[conversation.id] = conversation.unread_count;
                    }
                });
            }

            // Count from regular data
            data.data.forEach((conversation) => {
                if (conversation.unread_count !== undefined) {
                    counts[conversation.id] = conversation.unread_count;
                }
            });
            store.setAllUnreadCounts(counts);

            return loadMore ? data.data : [...(data.pinned || []), ...data.data];
        } finally {
            store.conversationsLoading = false;
        }
    }

    async function createConversation(type, participantIds, name = null, isPrivate = true) {
        const payload = {
            type,
            participant_ids: participantIds,
        };

        if (type === 'channel') {
            payload.name = name;
            payload.is_private = isPrivate;
        }

        const { data } = await api.post('/conversations', payload);
        store.addConversation(data.data);
        return data.data;
    }

    async function updateConversation(conversationId, updates) {
        const { data } = await api.patch(`/conversations/${conversationId}`, updates);
        store.updateConversation(conversationId, data.data);
        return data.data;
    }

    async function addParticipant(conversationId, userId) {
        const { data } = await api.post(`/conversations/${conversationId}/participants`, {
            user_id: userId,
        });
        return data.data;
    }

    async function fetchConversation(conversationId) {
        const { data } = await api.get(`/conversations/${conversationId}`);
        const exists = store.conversations.some((conversation) => conversation.id === conversationId);

        if (exists) {
            store.updateConversation(conversationId, data.data);
        } else {
            store.addConversation(data.data);
        }

        if (data.data.unread_count !== undefined) {
            store.setUnreadCount(conversationId, data.data.unread_count);
        }

        return data.data;
    }

    async function removeParticipant(conversationId, userId) {
        await api.delete(`/conversations/${conversationId}/participants/${userId}`);
    }

    async function updateParticipant(conversationId, userId, data) {
        const { data: response } = await api.patch(`/conversations/${conversationId}/participants/${userId}`, data);
        store.updateParticipant(conversationId, userId, response.data);
        return response.data;
    }

    async function leaveConversation(conversationId) {
        await api.post(`/conversations/${conversationId}/leave`);
        store.removeConversation(conversationId);
    }

    async function updateSettings(conversationId, settings) {
        const { data } = await api.patch(`/conversations/${conversationId}/settings`, settings);
        store.updateConversation(conversationId, settings);
        return data.data;
    }

    async function togglePin(conversationId) {
        const conversation = conversations.value.find(
            (existingConversation) => existingConversation.id === conversationId,
        );
        const newPinnedState = !conversation?.is_pinned;
        const { data } = await api.patch(`/conversations/${conversationId}/settings`, {
            is_pinned: newPinnedState,
        });
        store.updateConversation(conversationId, { is_pinned: data.data.is_pinned });
        return data.data;
    }

    async function markAsRead(conversationId) {
        // Clear any pending mark-as-read for a different conversation
        if (markAsReadTimeout.value) {
            clearTimeout(markAsReadTimeout.value);
            markAsReadTimeout.value = null;
        }

        // Update UI immediately
        store.markAsRead(conversationId);

        // Debounce the API call to avoid rapid requests when switching conversations
        pendingMarkAsRead.value = conversationId;
        markAsReadTimeout.value = setTimeout(async () => {
            if (pendingMarkAsRead.value === conversationId) {
                await api.post(`/conversations/${conversationId}/read`);
                pendingMarkAsRead.value = null;
            }
        }, 500);
    }

    async function fetchPublicChannels(search = '') {
        const params = search ? { search } : {};
        const { data } = await api.get('/conversations/public', { params });
        return data.data;
    }

    async function joinChannel(conversationId) {
        const { data } = await api.post(`/conversations/${conversationId}/join`);
        store.addConversation(data.data);
        return data.data;
    }

    return {
        conversations,
        loading,
        hasMore,
        loadConversations,
        createConversation,
        updateConversation,
        addParticipant,
        fetchConversation,
        removeParticipant,
        updateParticipant,
        leaveConversation,
        updateSettings,
        togglePin,
        markAsRead,
        fetchPublicChannels,
        joinChannel,
    };
}
