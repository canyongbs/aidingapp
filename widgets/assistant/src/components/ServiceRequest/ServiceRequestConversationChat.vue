<!--
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
-->
<script setup>
    import { PaperAirplaneIcon, UserIcon, XMarkIcon } from '@heroicons/vue/24/outline';
    import axios from 'axios';
    import { nextTick, onMounted, ref, watch } from 'vue';
    import { useConversationMessages } from '../../composables/useConversationMessages.js';
    import { getAuthHeaders } from '../../utils/token.js';

    const props = defineProps({
        conversationId: { type: String, required: true },
        websocketsConfig: { type: Object, default: null },
        authEndpoint: { type: String, default: null },
        serviceRequestTitle: { type: String, default: null },
        serviceRequestNumber: { type: String, default: null },
        agentName: { type: String, default: null },
        isEnded: { type: Boolean, default: false },
    });

    const showEndConfirm = ref(false);
    const isEnding = ref(false);

    async function endConversation() {
        if (isEnding.value) return;
        isEnding.value = true;
        try {
            await axios.post(
                `/widgets/assistant/api/conversations/${props.conversationId}/end`,
                {},
                {
                    headers: getAuthHeaders(),
                },
            );
        } finally {
            isEnding.value = false;
            showEndConfirm.value = false;
        }
    }

    const { messages, loading, sending, typingName, loadMessages, sendMessage, subscribe, broadcastTyping } =
        useConversationMessages(props.websocketsConfig, props.authEndpoint);

    const messageInput = ref('');
    const textarea = ref(null);
    const messagesContainer = ref(null);

    onMounted(async () => {
        await loadMessages(props.conversationId);
        subscribe(props.conversationId);
        scrollToBottom();
    });

    watch(messages, () => {
        nextTick(() => scrollToBottom());
    });

    watch(typingName, () => {
        nextTick(() => scrollToBottom());
    });

    function scrollToBottom() {
        if (messagesContainer.value) {
            messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
        }
    }

    function handleSend() {
        const body = messageInput.value.trim();
        if (!body) return;
        messageInput.value = '';
        nextTick(() => adjustTextareaHeight());
        sendMessage(props.conversationId, body);
    }

    function adjustTextareaHeight() {
        if (!textarea.value) return;
        textarea.value.style.height = 'auto';
        const lineHeight = 24;
        const padding = 24;
        const maxHeight = lineHeight * 3 + padding;
        const newHeight = Math.min(textarea.value.scrollHeight, maxHeight);
        textarea.value.style.height = `${newHeight}px`;
    }

    function handleInput() {
        adjustTextareaHeight();
        broadcastTyping();
    }

    function extractText(content) {
        if (!content) return '';
        if (typeof content === 'string') return content;
        if (content.type === 'doc' && Array.isArray(content.content)) {
            return content.content
                .map((node) => {
                    if (node.type === 'paragraph' && Array.isArray(node.content)) {
                        return node.content.map((inline) => inline.text || '').join('');
                    }
                    return '';
                })
                .join('\n');
        }
        return JSON.stringify(content);
    }

    function isOwnMessage(message) {
        return message.author_type === 'contact';
    }

    function shouldShowAuthorName(message, index) {
        if (isOwnMessage(message)) return false;
        if (!message.author_name) return false;
        if (index === 0) return true;

        const prevMessage = messages.value[index - 1];
        if (isOwnMessage(prevMessage)) return true;
        return prevMessage.author_name !== message.author_name;
    }
</script>

<template>
    <div class="flex flex-col h-full">
        <!-- Header -->
        <div class="flex items-center justify-between px-4 py-2.5 border-b border-gray-200 bg-gray-50 shrink-0">
            <span class="text-sm font-medium text-gray-700 truncate">
                {{ agentName ? `Chat with ${agentName}` : 'Live Chat' }}
            </span>
            <button
                v-if="!isEnded"
                type="button"
                class="flex items-center gap-1 text-xs font-medium text-red-600 hover:text-red-700 hover:bg-red-50 rounded px-2 py-1 transition-colors"
                @click="showEndConfirm = true"
            >
                <XMarkIcon class="w-3.5 h-3.5" />
                End
            </button>
        </div>

        <!-- End Confirmation -->
        <div
            v-if="showEndConfirm"
            class="flex items-center justify-between gap-3 px-4 py-2.5 bg-red-50 border-b border-red-200 shrink-0"
        >
            <span class="text-sm text-red-700">End this conversation?</span>
            <div class="flex gap-2 shrink-0">
                <button
                    type="button"
                    class="px-3 py-1 text-xs font-medium text-gray-600 hover:text-gray-800 transition-colors"
                    :disabled="isEnding"
                    @click="showEndConfirm = false"
                >
                    Cancel
                </button>
                <button
                    type="button"
                    class="px-3 py-1 text-xs font-medium text-white bg-red-600 hover:bg-red-700 rounded transition-colors"
                    :disabled="isEnding"
                    @click="endConversation"
                >
                    {{ isEnding ? 'Ending...' : 'End chat' }}
                </button>
            </div>
        </div>

        <!-- Messages -->
        <div ref="messagesContainer" class="flex-1 overflow-y-auto p-6 bg-white">
            <template v-if="loading">
                <div class="flex items-center justify-center h-full">
                    <div class="flex items-center space-x-1">
                        <div class="w-1.5 h-1.5 bg-brand-400 rounded-full animate-bounce"></div>
                        <div class="w-1.5 h-1.5 bg-brand-400 rounded-full animate-bounce [animation-delay:0.2s]"></div>
                        <div class="w-1.5 h-1.5 bg-brand-400 rounded-full animate-bounce [animation-delay:0.4s]"></div>
                    </div>
                </div>
            </template>

            <template v-else>
                <!-- Service Request Info Card -->
                <div v-if="serviceRequestTitle" class="mb-4 rounded-lg border border-brand-200 bg-brand-50 p-3">
                    <p class="text-sm font-medium text-gray-900 leading-snug">
                        {{ serviceRequestTitle }}
                    </p>
                </div>

                <template v-if="messages.length === 0">
                    <div class="flex items-center justify-center" :class="serviceRequestTitle ? 'py-6' : 'h-full'">
                        <p class="text-sm text-gray-500">
                            You are now connected{{ agentName ? ` with ${agentName}` : '' }}.
                        </p>
                    </div>
                </template>

                <div
                    v-for="(message, index) in messages"
                    :key="message.id"
                    class="flex gap-3"
                    :class="[
                        isOwnMessage(message) ? 'justify-end' : '',
                        index === 0
                            ? ''
                            : shouldShowAuthorName(message, index) ||
                                isOwnMessage(message) !== isOwnMessage(messages[index - 1])
                              ? 'mt-6'
                              : 'mt-1',
                    ]"
                >
                    <div v-if="!isOwnMessage(message)" class="shrink-0 w-8">
                        <div
                            v-if="shouldShowAuthorName(message, index)"
                            class="w-8 h-8 rounded-full bg-brand-100 flex items-center justify-center"
                        >
                            <UserIcon class="w-4 h-4 text-brand-600" />
                        </div>
                    </div>

                    <div class="max-w-[80%] flex flex-col" :class="isOwnMessage(message) ? 'items-end' : 'items-start'">
                        <div v-if="shouldShowAuthorName(message, index)" class="text-xs text-gray-500 mb-1 px-1">
                            {{ message.author_name }}
                        </div>
                        <div
                            class="px-4 py-3 shadow-xs border border-gray-200 text-left"
                            :class="
                                isOwnMessage(message)
                                    ? 'bg-brand-50 rounded-lg rounded-tr-sm'
                                    : 'bg-white rounded-lg rounded-tl-sm'
                            "
                        >
                            <p class="text-sm text-gray-800 leading-relaxed whitespace-pre-line">
                                {{ extractText(message.content) }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Typing Indicator -->
                <div v-if="typingName" class="flex items-center gap-2 mt-3">
                    <div class="flex space-x-1">
                        <span
                            class="inline-block h-1.5 w-1.5 animate-bounce rounded-full bg-gray-400 [animation-delay:-0.3s]"
                        />
                        <span
                            class="inline-block h-1.5 w-1.5 animate-bounce rounded-full bg-gray-400 [animation-delay:-0.15s]"
                        />
                        <span class="inline-block h-1.5 w-1.5 animate-bounce rounded-full bg-gray-400" />
                    </div>
                    <span class="text-xs text-gray-500">{{ typingName }} is typing...</span>
                </div>
            </template>
        </div>

        <!-- Ended Banner -->
        <div v-if="isEnded" class="border-t border-gray-200/80 bg-gray-50 p-4 text-center shrink-0">
            <p class="text-sm text-gray-500">This conversation has ended.</p>
        </div>

        <!-- Input -->
        <div v-else class="border-t border-gray-200/80 bg-white p-4 shadow-lg shrink-0">
            <div class="flex items-end gap-2">
                <textarea
                    ref="textarea"
                    v-model="messageInput"
                    @input="handleInput"
                    rows="1"
                    class="flex-1 resize-none rounded ring-1 ring-gray-400 px-4 py-3 text-sm leading-5 focus:outline-hidden focus:ring-2 focus:ring-primary-500 placeholder:text-gray-400 overflow-y-auto"
                    style="height: 44px"
                    placeholder="Type your message..."
                    :disabled="sending"
                    @keydown.enter.exact.prevent="handleSend"
                />
                <button
                    type="button"
                    class="bg-brand-600 hover:bg-brand-700 active:bg-brand-800 text-white rounded p-3 font-medium transition-all duration-150 focus:outline-hidden focus:ring-2 focus:ring-primary-500 shadow-xs hover:shadow disabled:opacity-50 disabled:cursor-not-allowed shrink-0"
                    :disabled="!messageInput.trim() || sending"
                    @click="handleSend"
                >
                    <PaperAirplaneIcon class="w-5 h-5" />
                </button>
            </div>
        </div>
    </div>
</template>
