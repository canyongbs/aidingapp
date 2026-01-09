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
import { computed, onUnmounted, ref, watch } from 'vue';
import { useAssistantStore } from '../../Stores/assistant.js';
import { useTokenStore } from '../../Stores/token.js';
import { useAssistantConnection } from './useAssistantConnection.js';

export function useAssistantChat() {
    const { assistantSendMessageUrl, websocketsConfig } = useAssistantStore();
    const { getToken } = useTokenStore();

    const messages = ref([]);
    const threadId = ref(null);
    const isSending = ref(false);
    const wordQueue = ref([]);
    const isTyping = ref(false);
    const activeWidget = ref(null);

    const isAssistantResponding = computed(() => {
        return messages.value.some((m) => m.author === 'assistant' && !m.isComplete);
    });

    const addUserMessage = (content) => {
        messages.value.push({ author: 'user', content, isComplete: true, error: null });
    };

    const addAssistantMessage = () => {
        messages.value.push({ author: 'assistant', content: '', isComplete: false, error: null });
        return messages.value.length - 1;
    };

    const typeNextWord = (messageIndex) => {
        if (wordQueue.value.length === 0) {
            isTyping.value = false;
            if (messages.value[messageIndex]?.shouldComplete) {
                messages.value[messageIndex].isComplete = true;
                delete messages.value[messageIndex].shouldComplete;
            }
            return;
        }

        isTyping.value = true;

        if (messageIndex === -1 || !messages.value[messageIndex]) {
            wordQueue.value = [];
            isTyping.value = false;
            return;
        }

        const word = wordQueue.value.shift();
        messages.value[messageIndex].content += word;

        const delay = word.trim() ? 50 : 0;
        setTimeout(() => typeNextWord(messageIndex), delay);
    };

    const updateAssistantMessage = (chunk, isComplete, error) => {
        const messageIndex = messages.value.findIndex((m) => m.author === 'assistant' && !m.isComplete);
        if (messageIndex === -1) return;

        if (chunk) {
            const words = chunk.split(/(\s+)/);
            wordQueue.value.push(...words);
            if (!isTyping.value) typeNextWord(messageIndex);
        }

        if (error) messages.value[messageIndex].error = error;

        if (isComplete && wordQueue.value.length === 0 && !isTyping.value) {
            messages.value[messageIndex].isComplete = true;
        } else if (isComplete) {
            messages.value[messageIndex].shouldComplete = true;
        }
    };

    const sendMessage = async (content, internalContent = null) => {
        if (!content || !content.trim() || isSending.value || isAssistantResponding.value) return;
        isSending.value = true;

        addUserMessage(content);

        const payload = { content, thread_id: threadId.value };
        if (internalContent) {
            payload.internal_content = internalContent;
        }

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

    const connection = useAssistantConnection(websocketsConfig, getToken);

    const handleActionRequest = (actionType, params) => {
        activeWidget.value = { type: actionType, params };
    };

    const connectToThread = async (id) => {
        if (!id) return;
        await connection.connect(
            id,
            (chunk, is_complete, err) => {
                updateAssistantMessage(chunk, is_complete, err);
            },
            handleActionRequest,
        );
    };

    const handleWidgetSubmit = async (submitData) => {
        const { type, field_id, type_id, value, display_text } = submitData;

        const internalContent = { type };
        if (field_id !== undefined) internalContent.field_id = field_id;
        if (type_id !== undefined) internalContent.type_id = type_id;
        if (value !== undefined) internalContent.value = value;

        await sendMessage(display_text || 'Submitted', internalContent);
        activeWidget.value = null;
    };

    const handleWidgetCancel = async () => {
        const widget = activeWidget.value;
        if (!widget) return;

        const internalContent = { type: 'widget_cancelled' };
        if (widget.params?.field_id) {
            internalContent.field_id = widget.params.field_id;
        }

        await sendMessage('Cancelled', internalContent);
        activeWidget.value = null;
    };

    watch(threadId, async (newId) => {
        if (!newId) return;
        await connectToThread(newId);
    });

    onUnmounted(() => {
        connection.disconnect();
    });

    return {
        messages,
        threadId,
        isSending,
        isAssistantResponding,
        sendMessage,
        activeWidget,
        handleWidgetSubmit,
        handleWidgetCancel,
    };
}
