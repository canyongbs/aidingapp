/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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
import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import { ref } from 'vue';

if (typeof window !== 'undefined') {
    window.Pusher = Pusher;
}

export function useAssistantConnection(
    websocketsConfig,
    getToken,
    guestToken,
    authEndpoint = '/api/broadcasting/auth',
) {
    const echo = ref(null);
    const currentThread = ref(null);

    const connect = async (threadId, onChunk) => {
        if (!websocketsConfig || !threadId) return;

        if (echo.value && currentThread.value) {
            echo.value.leave(`portal-assistant-thread-${currentThread.value}`);
        }

        if (!echo.value) {
            echo.value = new Echo({
                ...websocketsConfig,
                authorizer: (channel, options) => {
                    return {
                        authorize: async (socketId, callback) => {
                            const token = getToken ? await getToken() : null;
                            const headers = {
                                'Content-Type': 'application/json',
                            };

                            if (token) {
                                headers['Authorization'] = `Bearer ${token}`;
                            }

                            const body = {
                                socket_id: socketId,
                                channel_name: channel.name,
                            };

                            if (guestToken) {
                                body.guest_token = guestToken;
                            }

                            axios
                                .post(authEndpoint, body, { headers })
                                .then((response) => {
                                    callback(false, response.data);
                                })
                                .catch((error) => {
                                    callback(true, error);
                                });
                        },
                    };
                },
            });

            if (echo.value.connector?.pusher) {
                echo.value.connector.pusher.connection.bind('connected', () => {
                    echo.value.connector.pusher.config.activityTimeout = 120000;
                    echo.value.connector.pusher.config.pongTimeout = 30000;
                });
            }
        }

        currentThread.value = threadId;

        const channel = echo.value.private(`portal-assistant-thread-${threadId}`);

        channel.listen('.portal-assistant-message.chunk', (event) => {
            if (onChunk) {
                onChunk(event.content || '', event.is_complete, event.error);
            }
        });
    };

    const leave = (threadId) => {
        if (echo.value && threadId) {
            echo.value.leave(`portal-assistant-thread-${threadId}`);
        }
    };

    const disconnect = () => {
        if (echo.value) {
            if (currentThread.value) {
                echo.value.leave(`portal-assistant-thread-${currentThread.value}`);
            }
            echo.value.disconnect();
            echo.value = null;
            currentThread.value = null;
        }
    };

    return { connect, leave, disconnect, echo, currentThread };
}
