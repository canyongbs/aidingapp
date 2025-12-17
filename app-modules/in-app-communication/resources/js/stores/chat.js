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

import { defineStore } from 'pinia';
import { computed, ref } from 'vue';

export const useChatStore = defineStore('chat', () => {
    const conversations = ref([]);
    const conversationsNextCursor = ref(null);
    const conversationsHasMore = ref(false);
    const messages = ref({});
    const typingUsers = ref({});
    const unreadCounts = ref({});
    const currentUser = ref({ id: null, name: null, avatar: null });
    const selectedConversationId = ref(null);
    const conversationsLoading = ref(false);

    const selectedConversation = computed(() =>
        conversations.value.find((conversation) => conversation.id === selectedConversationId.value),
    );

    const selectedConversationMessages = computed(() => messages.value[selectedConversationId.value] || []);

    const selectedConversationTypingUsers = computed(() => typingUsers.value[selectedConversationId.value] || []);

    function setCurrentUser(user) {
        currentUser.value = user;
    }

    function setConversations(data) {
        conversations.value = data;
    }

    function appendConversations(data) {
        const existingIds = new Set(conversations.value.map((conversation) => conversation.id));
        const newConversations = data.filter((conversation) => !existingIds.has(conversation.id));
        conversations.value = [...conversations.value, ...newConversations];
    }

    function setConversationsPagination(nextCursor, hasMore) {
        conversationsNextCursor.value = nextCursor;
        conversationsHasMore.value = hasMore;
    }

    function addConversation(conversation) {
        const exists = conversations.value.find((existingConversation) => existingConversation.id === conversation.id);
        if (!exists) {
            conversations.value.unshift(conversation);
        }
    }

    function updateConversation(conversationId, data) {
        const index = conversations.value.findIndex((conversation) => conversation.id === conversationId);
        if (index !== -1) {
            conversations.value[index] = { ...conversations.value[index], ...data };
        }
    }

    function updateParticipant(conversationId, participantId, data) {
        const conversation = conversations.value.find(
            (existingConversation) => existingConversation.id === conversationId,
        );
        if (conversation && conversation.participants) {
            const participant = conversation.participants.find(
                (existingParticipant) => existingParticipant.participant_id === participantId,
            );
            if (participant) {
                Object.assign(participant, data);
            }
        }
    }

    function addParticipant(conversationId, participant) {
        const conversation = conversations.value.find(
            (existingConversation) => existingConversation.id === conversationId,
        );
        if (conversation) {
            if (!conversation.participants) {
                conversation.participants = [];
            }
            const exists = conversation.participants.find(
                (existingParticipant) => existingParticipant.participant_id === participant.participant_id,
            );
            if (!exists) {
                conversation.participants.push(participant);
                if (conversation.participant_count !== undefined) {
                    conversation.participant_count++;
                }
            }
        }
    }

    function removeParticipant(conversationId, participantId) {
        const conversation = conversations.value.find(
            (existingConversation) => existingConversation.id === conversationId,
        );
        if (conversation && conversation.participants) {
            conversation.participants = conversation.participants.filter(
                (existingParticipant) => existingParticipant.participant_id !== participantId,
            );
            if (conversation.participant_count !== undefined) {
                conversation.participant_count--;
            }
        }
    }

    function removeConversation(conversationId) {
        conversations.value = conversations.value.filter((conversation) => conversation.id !== conversationId);
        if (selectedConversationId.value === conversationId) {
            selectedConversationId.value = null;
        }
    }

    function setMessages(conversationId, data) {
        messages.value[conversationId] = data;
    }

    function addMessage(conversationId, message) {
        if (!messages.value[conversationId]) {
            messages.value[conversationId] = [];
        }
        const exists = messages.value[conversationId].find((existingMessage) => existingMessage.id === message.id);
        if (!exists) {
            messages.value[conversationId].push(message);
        }

        const conversation = conversations.value.find(
            (existingConversation) => existingConversation.id === conversationId,
        );
        if (conversation) {
            conversation.last_message = message;
            const index = conversations.value.findIndex(
                (existingConversation) => existingConversation.id === conversationId,
            );
            if (index > 0) {
                conversations.value.splice(index, 1);
                conversations.value.unshift(conversation);
            }
        }
    }

    function prependMessages(conversationId, newMessages) {
        if (!messages.value[conversationId]) {
            messages.value[conversationId] = [];
        }
        messages.value[conversationId] = [...newMessages, ...messages.value[conversationId]];
    }

    function updateMessage(conversationId, tempId, updatedMessage) {
        if (!messages.value[conversationId]) return;

        const index = messages.value[conversationId].findIndex((message) => message.id === tempId);
        if (index !== -1) {
            messages.value[conversationId][index] = updatedMessage;
        }
    }

    function setMessageFailed(conversationId, tempId) {
        if (!messages.value[conversationId]) return;

        const index = messages.value[conversationId].findIndex((message) => message.id === tempId);
        if (index !== -1) {
            messages.value[conversationId][index] = {
                ...messages.value[conversationId][index],
                _failed: true,
                _sending: false,
            };
        }
    }

    function removeMessage(conversationId, messageId) {
        if (!messages.value[conversationId]) return;

        messages.value[conversationId] = messages.value[conversationId].filter((message) => message.id !== messageId);
    }

    function setMessageRetrying(conversationId, messageId) {
        if (!messages.value[conversationId]) return;

        const index = messages.value[conversationId].findIndex((message) => message.id === messageId);
        if (index !== -1) {
            messages.value[conversationId][index] = {
                ...messages.value[conversationId][index],
                _failed: false,
                _sending: true,
            };
        }
    }

    function setTyping(conversationId, userId, isTyping) {
        if (!typingUsers.value[conversationId]) {
            typingUsers.value[conversationId] = [];
        }

        const userIndex = typingUsers.value[conversationId].indexOf(userId);

        if (isTyping && userIndex === -1) {
            typingUsers.value[conversationId].push(userId);
        } else if (!isTyping && userIndex !== -1) {
            typingUsers.value[conversationId].splice(userIndex, 1);
        }
    }

    function clearTyping(conversationId, userId) {
        setTyping(conversationId, userId, false);
    }

    function clearAllTyping(conversationId) {
        typingUsers.value[conversationId] = [];
    }

    function setUnreadCount(conversationId, count) {
        unreadCounts.value = { ...unreadCounts.value, [conversationId]: count };
    }

    function setAllUnreadCounts(counts) {
        unreadCounts.value = { ...counts };
    }

    function incrementUnread(conversationId) {
        const currentCount = unreadCounts.value[conversationId] || 0;
        unreadCounts.value = { ...unreadCounts.value, [conversationId]: currentCount + 1 };
    }

    function markAsRead(conversationId) {
        unreadCounts.value = { ...unreadCounts.value, [conversationId]: 0 };
    }

    function selectConversation(conversationId) {
        selectedConversationId.value = conversationId;
        if (conversationId) {
            markAsRead(conversationId);
            clearAllTyping(conversationId);
        }
    }

    return {
        conversations,
        conversationsNextCursor,
        conversationsHasMore,
        messages,
        typingUsers,
        unreadCounts,
        currentUser,
        selectedConversationId,
        conversationsLoading,
        selectedConversation,
        selectedConversationMessages,
        selectedConversationTypingUsers,
        setCurrentUser,
        setConversations,
        appendConversations,
        setConversationsPagination,
        addConversation,
        updateConversation,
        addParticipant,
        updateParticipant,
        removeParticipant,
        removeConversation,
        setMessages,
        addMessage,
        prependMessages,
        updateMessage,
        setMessageFailed,
        removeMessage,
        setMessageRetrying,
        setTyping,
        clearTyping,
        clearAllTyping,
        setUnreadCount,
        setAllUnreadCounts,
        incrementUnread,
        markAsRead,
        selectConversation,
    };
});
