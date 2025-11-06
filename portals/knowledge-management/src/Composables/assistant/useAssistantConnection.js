import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import { ref } from 'vue';

if (typeof window !== 'undefined') {
    window.Pusher = Pusher;
}

export function useAssistantConnection(websocketsConfig, getToken) {
    const echo = ref(null);
    const currentThread = ref(null);

    const connect = async (threadId, onChunk) => {
        if (!websocketsConfig || !threadId) return;

        if (echo.value && currentThread.value) {
            echo.value.leave(`portal-assistant-thread-${currentThread.value}`);
        }

        if (!echo.value) {
            const token = await getToken();

            echo.value = new Echo({
                ...websocketsConfig,
                authorizer: (channel, options) => {
                    return {
                        authorize: async (socketId, callback) => {
                            axios
                                .post(
                                    '/api/broadcasting/auth',
                                    {
                                        socket_id: socketId,
                                        channel_name: channel.name,
                                    },
                                    {
                                        headers: {
                                            'Content-Type': 'application/json',
                                            Authorization: `Bearer ${token}`,
                                        },
                                    },
                                )
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
                    // Disable activity timeout to prevent throttling when page is not visible
                    echo.value.connector.pusher.config.activityTimeout = 120000;
                    echo.value.connector.pusher.config.pongTimeout = 30000;
                });
            }
        }

        currentThread.value = threadId;

        echo.value.private(`portal-assistant-thread-${threadId}`).listen('.portal-assistant-message.chunk', (event) => {
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
