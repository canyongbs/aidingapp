/*
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
*/
import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import { onUnmounted, ref } from 'vue';
import { getAuthHeaders } from '../utils/token.js';

if (typeof window !== 'undefined') {
    window.Pusher = Pusher;
}

export function useConversationMessages(websocketsConfig, authEndpoint) {
    const messages = ref([]);
    const loading = ref(false);
    const hasMore = ref(false);
    const sending = ref(false);
    const typingName = ref(null);

    let echo = null;
    let channelName = null;
    let typingTimeout = null;
    let typingThrottleTimer = null;

    async function loadMessages(conversationId) {
        if (!conversationId) return;

        loading.value = true;

        try {
            const url = `/widgets/assistant/api/conversations/${conversationId}/messages`;
            const response = await axios.get(url, { headers: getAuthHeaders() });

            messages.value = response.data.data;
            hasMore.value = response.data.meta.has_more;
        } catch {
            messages.value = [];
        } finally {
            loading.value = false;
        }
    }

    async function sendMessage(conversationId, body) {
        if (!conversationId || !body.trim() || sending.value) return;

        sending.value = true;

        try {
            const url = `/widgets/assistant/api/conversations/${conversationId}/messages`;
            const response = await axios.post(url, { body }, { headers: getAuthHeaders() });

            const newMessage = response.data.data;
            const exists = messages.value.some((m) => m.id === newMessage.id);
            if (!exists) {
                messages.value = [...messages.value, newMessage];
            }
        } finally {
            sending.value = false;
        }
    }

    function subscribe(conversationId) {
        if (!websocketsConfig || !conversationId) return;

        channelName = `conversation.${conversationId}`;

        echo = new Echo({
            ...websocketsConfig,
            authorizer: (channel) => {
                return {
                    authorize: async (socketId, callback) => {
                        const headers = {
                            'Content-Type': 'application/json',
                            ...getAuthHeaders(),
                        };

                        axios
                            .post(
                                authEndpoint,
                                {
                                    socket_id: socketId,
                                    channel_name: channel.name,
                                },
                                { headers },
                            )
                            .then((response) => {
                                callback(false, response.data);
                            })
                            .catch((err) => callback(true, err));
                    },
                };
            },
        });

        echo.join(channelName)
            .listen('.message.sent', (event) => {
                const exists = messages.value.some((m) => m.id === event.id);
                if (!exists) {
                    messages.value = [...messages.value, event];
                }
                typingName.value = null;
            })
            .listenForWhisper('typing', (event) => {
                typingName.value = event.user_name || 'Agent';

                if (typingTimeout) clearTimeout(typingTimeout);
                typingTimeout = setTimeout(() => {
                    typingName.value = null;
                }, 4000);
            });
    }

    function broadcastTyping() {
        if (!channelName) return;
        if (typingThrottleTimer) return;

        const conversationId = channelName.replace('conversation.', '');
        const url = `/widgets/assistant/api/conversations/${conversationId}/typing`;
        axios.post(url, {}, { headers: getAuthHeaders() }).catch(() => {});

        typingThrottleTimer = setTimeout(() => {
            typingThrottleTimer = null;
        }, 2500);
    }

    function cleanup() {
        if (echo && channelName) {
            echo.leave(channelName);
        }
        if (echo) {
            echo.disconnect();
            echo = null;
        }
        channelName = null;
        if (typingTimeout) {
            clearTimeout(typingTimeout);
            typingTimeout = null;
        }
        if (typingThrottleTimer) {
            clearTimeout(typingThrottleTimer);
            typingThrottleTimer = null;
        }
    }

    onUnmounted(() => {
        cleanup();
    });

    return {
        messages,
        loading,
        hasMore,
        sending,
        typingName,
        loadMessages,
        sendMessage,
        subscribe,
        broadcastTyping,
        cleanup,
    };
}
