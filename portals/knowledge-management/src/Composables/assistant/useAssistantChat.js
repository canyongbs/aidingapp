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

    const sendMessage = async (content) => {
        if (!content || !content.trim() || isSending.value || isAssistantResponding.value) return;
        isSending.value = true;

        addUserMessage(content);

        const payload = { content, thread_id: threadId.value };

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

    const connectToThread = async (id) => {
        if (!id) return;
        await connection.connect(id, (chunk, is_complete, err) => {
            updateAssistantMessage(chunk, is_complete, err);
        });
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
    };
}
