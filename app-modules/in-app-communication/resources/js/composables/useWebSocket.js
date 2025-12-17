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

import { ref } from 'vue';
import { useChatStore } from '../stores/chat';

export function useWebSocket() {
    const subscribedChannels = ref(new Set());
    const userChannelSubscribed = ref(false);
    const currentPresenceChannel = ref(null);
    const store = useChatStore();

    function getEcho() {
        return window.Echo;
    }

    function subscribeToUserChannel(userId, { onNewConversation, onUnknownConversation } = {}) {
        const echo = getEcho();
        if (!echo || userChannelSubscribed.value) {
            return;
        }

        echo.private(`user.${userId}`)
            .listen('.conversation.created', (event) => {
                store.addConversation(event);
                subscribeToConversation(event.id);

                if (onNewConversation) {
                    onNewConversation(event);
                }
            })
            .listen('.message.sent', async (event) => {
                if (event.author_id !== store.currentUser.id) {
                    const conversationExists = store.conversations.some(
                        (conversation) => conversation.id === event.conversation_id,
                    );

                    if (!conversationExists && onUnknownConversation) {
                        await onUnknownConversation(event.conversation_id);
                    }

                    store.addMessage(event.conversation_id, event);
                }
            })
            .listen('.unread-count.updated', (event) => {
                store.setUnreadCount(event.conversationId, event.unreadCount);
            })
            .listen('.participant.removed', (event) => {
                if (event.user_id === userId) {
                    unsubscribeFromConversation(event.conversation_id);
                    store.removeConversation(event.conversation_id);
                }
            })
            .listen('.participant.added', (event) => {
                if (event.participant_id === userId && event.conversation) {
                    store.addConversation(event.conversation);
                    subscribeToConversation(event.conversation.id);
                }
            });

        userChannelSubscribed.value = true;
    }

    function subscribeToConversation(conversationId) {
        const echo = getEcho();
        if (!echo || subscribedChannels.value.has(conversationId)) {
            return;
        }

        // Use private channel for participant and conversation update events
        echo.private(`conversation.${conversationId}`)
            .listen('.participant.added', (event) => {
                if (event.participant_id !== store.currentUser.id) {
                    store.addParticipant(event.conversation_id, event);
                }
            })
            .listen('.participant.removed', (event) => {
                if (event.user_id !== store.currentUser.id) {
                    store.removeParticipant(event.conversation_id, event.user_id);
                }
            })
            .listen('.participant.updated', (event) => {
                store.updateParticipant(event.conversation_id, event.participant_id, {
                    is_manager: event.is_manager,
                });
            })
            .listen('.conversation.updated', (event) => {
                store.updateConversation(event.id, {
                    name: event.name,
                    display_name: event.display_name,
                    is_private: event.is_private,
                });
            });

        subscribedChannels.value.add(conversationId);
    }

    function joinPresence(conversationId) {
        const echo = getEcho();
        if (!echo) return;

        // Leave previous presence channel if any
        if (currentPresenceChannel.value && currentPresenceChannel.value !== conversationId) {
            const previousConversationId = currentPresenceChannel.value;
            echo.leave(`conversation.${previousConversationId}`);

            // Re-subscribe to private channel since echo.leave() unsubscribes from both
            // private and presence channels with the same name
            subscribedChannels.value.delete(previousConversationId);
            subscribeToConversation(previousConversationId);
        }

        // Join presence channel for the selected conversation
        if (conversationId) {
            echo.join(`conversation.${conversationId}`).listenForWhisper('typing', (event) => {
                if (event.user_id !== store.currentUser.id) {
                    store.setTyping(conversationId, event.user_id, true);

                    setTimeout(() => {
                        store.clearTyping(conversationId, event.user_id);
                    }, 4000);
                }
            });

            currentPresenceChannel.value = conversationId;
        } else {
            currentPresenceChannel.value = null;
        }
    }

    function leavePresence() {
        const echo = getEcho();
        if (!echo || !currentPresenceChannel.value) return;

        const previousConversationId = currentPresenceChannel.value;
        echo.leave(`conversation.${previousConversationId}`);
        currentPresenceChannel.value = null;

        // Re-subscribe to private channel since echo.leave() unsubscribes from both
        subscribedChannels.value.delete(previousConversationId);
        subscribeToConversation(previousConversationId);
    }

    function unsubscribeFromConversation(conversationId) {
        const echo = getEcho();
        if (!echo || !subscribedChannels.value.has(conversationId)) {
            return;
        }

        echo.leave(`conversation.${conversationId}`);
        subscribedChannels.value.delete(conversationId);
    }

    function subscribeToAllConversations(conversations) {
        conversations.forEach((conversation) => {
            subscribeToConversation(conversation.id);
        });
    }

    function disconnect() {
        const echo = getEcho();
        if (!echo) return;

        // Leave presence channel
        if (currentPresenceChannel.value) {
            echo.leave(`conversation.${currentPresenceChannel.value}`);
            currentPresenceChannel.value = null;
        }

        // Leave all private channels
        subscribedChannels.value.forEach((conversationId) => {
            echo.leave(`conversation.${conversationId}`);
        });
        subscribedChannels.value.clear();

        if (userChannelSubscribed.value) {
            echo.leave(`user.${store.currentUser.id}`);
            userChannelSubscribed.value = false;
        }
    }

    return {
        subscribedChannels,
        subscribeToUserChannel,
        subscribeToConversation,
        unsubscribeFromConversation,
        subscribeToAllConversations,
        joinPresence,
        leavePresence,
        disconnect,
    };
}
