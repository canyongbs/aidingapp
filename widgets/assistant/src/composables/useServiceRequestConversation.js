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
*/
import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import { onUnmounted, ref } from 'vue';
import { getAuthHeaders } from '../utils/token.js';

if (typeof window !== 'undefined') {
    window.Pusher = Pusher;
}

export function useServiceRequestConversation(websocketsConfig, authEndpoint) {
    const eligible = ref(false);
    const agentName = ref(null);
    const status = ref('idle');
    const conversationId = ref(null);
    const error = ref(null);

    let echo = null;
    let channelName = null;

    async function checkEligibility(serviceRequestId) {
        if (!serviceRequestId) return;

        status.value = 'checking';
        error.value = null;

        try {
            const url = `/widgets/assistant/api/service-request/${serviceRequestId}/conversation/eligibility`;
            const response = await axios.get(url, { headers: getAuthHeaders() });

            eligible.value = response.data.eligible;
            agentName.value = response.data.agent_name ?? null;
            status.value = 'idle';
        } catch (e) {
            eligible.value = false;
            status.value = 'idle';
        }
    }

    async function requestConversation(serviceRequestId) {
        if (!serviceRequestId) return;

        status.value = 'queued';
        error.value = null;

        try {
            const url = `/widgets/assistant/api/service-request/${serviceRequestId}/conversation`;
            const response = await axios.post(url, {}, { headers: getAuthHeaders() });

            const recordId = response.data.id;
            subscribeToChannel(recordId);
        } catch (e) {
            if (e.response?.status === 422) {
                error.value = Object.values(e.response.data.errors ?? {})[0]?.[0] ?? 'Unable to connect.';
            } else {
                error.value = 'Something went wrong. Please try again.';
            }
            status.value = 'error';
        }
    }

    function subscribeToChannel(recordId) {
        if (!websocketsConfig) return;

        channelName = `service-request-conversation.${recordId}`;

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
                            .then((response) => callback(false, response.data))
                            .catch((err) => callback(true, err));
                    },
                };
            },
        });

        const channel = echo.private(channelName);

        channel.listen('.service-request-conversation.accepted', (event) => {
            status.value = 'accepted';
            conversationId.value = event.conversation_id;
        });

        channel.listen('.service-request-conversation.declined', () => {
            status.value = 'declined';
            cleanup();
        });

        channel.listen('.service-request-conversation.expired', () => {
            status.value = 'expired';
            cleanup();
        });
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
    }

    onUnmounted(() => {
        cleanup();
    });

    return {
        eligible,
        agentName,
        status,
        conversationId,
        error,
        checkEligibility,
        requestConversation,
        cleanup,
    };
}
