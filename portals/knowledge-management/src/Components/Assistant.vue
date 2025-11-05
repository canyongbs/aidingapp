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
    import { ChatBubbleLeftRightIcon, ChevronDownIcon, PaperAirplaneIcon, XMarkIcon } from '@heroicons/vue/24/outline';
    import axios from 'axios';
    import Echo from 'laravel-echo';
    import Pusher from 'pusher-js';
    import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
    import { useAssistantStore } from '../Stores/assistant.js';
    import { useAuthStore } from '../Stores/auth.js';
    import { useTokenStore } from '../Stores/token.js';

    window.Pusher = Pusher;

    const { assistantSendMessageUrl, websocketsConfig } = useAssistantStore();
    const authStore = useAuthStore();
    const { getToken } = useTokenStore();

    const isOpen = ref(false);
    const message = ref('');
    const textarea = ref(null);
    const messages = ref([]);
    const threadId = ref(null);
    const echo = ref(null);
    const isSending = ref(false);

    const firstName = computed(() => {
        return authStore.user?.name?.split(' ')[0] || 'there';
    });

    const welcomeMessage = computed(() => {
        return `Hi ${firstName.value}, I am your support assistant. I can help you find information and troubleshoot issues. How can I assist you today?`;
    });

    const toggleChat = () => {
        isOpen.value = !isOpen.value;
    };

    const adjustTextareaHeight = () => {
        if (!textarea.value) {
            return;
        }

        textarea.value.style.height = 'auto';

        const lineHeight = 24;
        const padding = 24;
        const maxHeight = lineHeight * 3 + padding;
        const newHeight = Math.min(textarea.value.scrollHeight, maxHeight);

        textarea.value.style.height = `${newHeight}px`;
    };

    const handleInput = () => {
        adjustTextareaHeight();
    };

    const addUserMessage = (content) => {
        messages.value.push({
            author: 'user',
            content,
            isComplete: true,
            error: null,
        });
    };

    const addAssistantMessage = () => {
        messages.value.push({
            author: 'assistant',
            content: '',
            isComplete: false,
            error: null,
        });

        return messages.value.length - 1;
    };

    const updateAssistantMessage = (chunk, isComplete, error) => {
        const messageIndex = messages.value.findIndex(
            (message) => message.author === 'assistant' && !message.isComplete,
        );

        if (messageIndex === -1) {
            return;
        }

        messages.value[messageIndex].content += chunk;

        if (isComplete) {
            messages.value[messageIndex].isComplete = true;
        }

        if (error) {
            messages.value[messageIndex].error = error;
        }
    };

    const sendMessage = async () => {
        if (!message.value.trim() || isSending.value) {
            return;
        }

        isSending.value = true;
        addUserMessage(message.value);

        const payload = {
            content: message.value,
            thread_id: threadId.value,
        };

        message.value = '';
        adjustTextareaHeight();

        try {
            const response = await axios.post(assistantSendMessageUrl, payload);

            if (response.data.thread_id) {
                threadId.value = response.data.thread_id;
            }

            addAssistantMessage();
        } catch (error) {
            updateAssistantMessage('', true, 'Failed to send message.');
        } finally {
            isSending.value = false;
        }
    };

    const setupWebsocket = async () => {
        if (!websocketsConfig || !threadId.value) {
            return;
        }

        if (echo.value) {
            echo.value.leave(`portal-assistant-thread-${threadId.value}`);
        }

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

        echo.value
            .private(`portal-assistant-thread-${threadId.value}`)
            .listen('.portal-assistant-message.chunk', (event) => {
                updateAssistantMessage(event.content || '', event.is_complete, event.error);
            });
    };

    onMounted(async () => {
        if (!assistantSendMessageUrl || !websocketsConfig || !threadId.value) {
            return;
        }

        await setupWebsocket();
    });

    onUnmounted(() => {
        if (!echo.value) {
            return;
        }

        if (threadId.value) {
            echo.value.leave(`portal-assistant-thread-${threadId.value}`);
        }

        echo.value.disconnect();
    });

    watch(threadId, async (newThreadId) => {
        if (!newThreadId) {
            return;
        }

        await setupWebsocket();
    });
</script>

<template>
    <div
        v-show="assistantSendMessageUrl"
        class="fixed bottom-4 end-4 z-50 flex flex-col items-end max-h-[calc(100vh-2rem)] max-w-[calc(100vw-2rem)]"
    >
        <div
            v-if="isOpen"
            class="mb-4 w-[400px] max-w-full h-[650px] max-h-full bg-white rounded-2xl shadow-2xl flex flex-col overflow-hidden border border-gray-200/80 backdrop-blur-sm"
        >
            <div
                class="bg-gradient-to-r from-brand-600 to-brand-700 text-white px-6 py-4 flex items-center justify-between shadow-md shrink-0"
            >
                <div class="flex items-center gap-3">
                    <div class="bg-white/20 p-2 rounded-lg">
                        <ChatBubbleLeftRightIcon class="w-5 h-5" />
                    </div>
                    <h2 class="text-lg font-semibold tracking-tight">Assistant</h2>
                </div>
                <button
                    @click="toggleChat"
                    class="text-white/90 hover:text-white hover:bg-white/10 transition-all rounded-lg p-1.5"
                    aria-label="Close chat"
                >
                    <XMarkIcon class="w-5 h-5" />
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-6 bg-gradient-to-b from-gray-50 to-white">
                <div class="flex gap-3 mb-4">
                    <div class="shrink-0">
                        <div class="w-8 h-8 rounded-full bg-brand-100 flex items-center justify-center">
                            <ChatBubbleLeftRightIcon class="w-4 h-4 text-brand-600" />
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="bg-white rounded-2xl rounded-ts-sm px-4 py-3 shadow-sm border border-gray-200">
                            <p class="text-sm text-gray-800 leading-relaxed">{{ welcomeMessage }}</p>
                        </div>
                    </div>
                </div>
                <div
                    v-for="(chatMessage, index) in messages"
                    :key="index"
                    class="flex gap-3 mb-4"
                    :class="chatMessage.author === 'user' ? 'justify-end' : ''"
                >
                    <div v-if="chatMessage.author === 'assistant'" class="shrink-0">
                        <div class="w-8 h-8 rounded-full bg-brand-100 flex items-center justify-center">
                            <ChatBubbleLeftRightIcon class="w-4 h-4 text-brand-600" />
                        </div>
                    </div>

                    <div class="flex-1 max-w-[80%]">
                        <div
                            :class="
                                chatMessage.author === 'assistant'
                                    ? 'bg-white rounded-2xl rounded-ts-sm'
                                    : 'bg-brand-50 rounded-2xl rounded-te-sm'
                            "
                            class="px-4 py-3 shadow-sm border border-gray-200"
                        >
                            <p class="text-sm text-gray-800 leading-relaxed whitespace-pre-line">
                                {{ chatMessage.content }}
                            </p>

                            <div v-if="chatMessage.error" class="text-xs text-red-500 mt-1">
                                {{ chatMessage.error }}
                            </div>

                            <div
                                v-if="chatMessage.author === 'assistant' && !chatMessage.isComplete"
                                class="text-xs text-gray-400 mt-1"
                            >
                                Assistant is typing…
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-200/80 bg-white p-4 shadow-lg shrink-0">
                <div class="flex items-end gap-2">
                    <textarea
                        ref="textarea"
                        v-model="message"
                        @input="handleInput"
                        placeholder="Type your message..."
                        rows="1"
                        class="flex-1 resize-none rounded-xl border border-gray-300 px-4 py-3 text-sm leading-6 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent placeholder:text-gray-400 overflow-y-auto"
                        style="min-height: 42px"
                        @keydown.enter.exact.prevent="sendMessage"
                        :disabled="isSending"
                    ></textarea>
                    <button
                        class="bg-brand-600 hover:bg-brand-700 active:bg-brand-800 text-white rounded-xl p-3 font-medium transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 shadow-sm hover:shadow disabled:opacity-50 disabled:cursor-not-allowed shrink-0"
                        aria-label="Send message"
                        @click="sendMessage"
                        :disabled="isSending || !message.trim()"
                    >
                        <PaperAirplaneIcon class="w-5 h-5" />
                    </button>
                </div>
            </div>
        </div>

        <button
            @click="toggleChat"
            class="bg-gradient-to-br from-brand-600 to-brand-700 hover:from-brand-700 hover:to-brand-800 text-white rounded-full p-4 shadow-xl hover:shadow-2xl transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-brand-500/50 hover:scale-105 active:scale-95"
            aria-label="Toggle chat assistant"
        >
            <ChatBubbleLeftRightIcon v-if="!isOpen" class="w-6 h-6" />
            <ChevronDownIcon v-else class="w-6 h-6" />
        </button>
    </div>
</template>
