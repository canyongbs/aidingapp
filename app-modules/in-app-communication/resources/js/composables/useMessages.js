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

import { computed, ref, watch } from 'vue';
import api from '../services/api';
import { useChatStore } from '../stores/chat';

export function useMessages(conversationId) {
    const store = useChatStore();
    const loading = ref(false);
    const hasMore = ref(true);

    const messages = computed(() => store.messages[conversationId.value] || []);

    async function loadMessages(loadMore = false) {
        if (!conversationId.value || loading.value) return;

        loading.value = true;
        try {
            const params = {};

            // When loading more, get messages before the oldest message we have
            if (loadMore && messages.value.length > 0) {
                const oldestMessage = messages.value[0];
                params.before = oldestMessage.id;
            }

            const { data } = await api.get(`/conversations/${conversationId.value}/messages`, { params });

            // Messages come in desc order from API, reverse to get chronological order
            const newMessages = data.data.reverse();

            if (loadMore) {
                store.prependMessages(conversationId.value, newMessages);
            } else {
                store.setMessages(conversationId.value, newMessages);
            }

            hasMore.value = data.meta?.has_more ?? false;
        } finally {
            loading.value = false;
        }
    }

    async function sendMessage(content) {
        if (!conversationId.value) return;

        // Create optimistic message
        const tempId = `temp-${Date.now()}-${Math.random().toString(36).slice(2)}`;
        const optimisticMessage = {
            id: tempId,
            conversation_id: conversationId.value,
            author_type: 'user',
            author_id: store.currentUser.id,
            author_name: store.currentUser.name,
            author_avatar: store.currentUser.avatar,
            content,
            created_at: new Date().toISOString(),
            _sending: true,
        };

        // Add message immediately
        store.addMessage(conversationId.value, optimisticMessage);

        try {
            const { data } = await api.post(`/conversations/${conversationId.value}/messages`, {
                content,
            });

            // Replace temp message with real one
            store.updateMessage(conversationId.value, tempId, data.data);
            return data.data;
        } catch (error) {
            // Mark message as failed
            store.setMessageFailed(conversationId.value, tempId);
            throw error;
        }
    }

    async function retryMessage(messageId) {
        if (!conversationId.value) return;

        const message = messages.value.find((existingMessage) => existingMessage.id === messageId);
        if (!message || !message._failed) return;

        // Mark as retrying
        store.setMessageRetrying(conversationId.value, messageId);

        try {
            const { data } = await api.post(`/conversations/${conversationId.value}/messages`, {
                content: message.content,
            });

            // Replace temp message with real one
            store.updateMessage(conversationId.value, messageId, data.data);
            return data.data;
        } catch (error) {
            // Mark as failed again
            store.setMessageFailed(conversationId.value, messageId);
            throw error;
        }
    }

    function removeFailedMessage(messageId) {
        if (!conversationId.value) return;
        store.removeMessage(conversationId.value, messageId);
    }

    watch(conversationId, (newId, oldId) => {
        if (newId !== oldId) {
            hasMore.value = true;
            if (newId && !store.messages[newId]?.length) {
                loadMessages();
            }
        }
    });

    return {
        messages,
        loading,
        hasMore,
        loadMessages,
        sendMessage,
        retryMessage,
        removeFailedMessage,
    };
}
