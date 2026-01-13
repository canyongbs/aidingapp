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
import { useFileUpload } from './useFileUpload.js';

export function useAssistantChat() {
    const { assistantSendMessageUrl, selectTypeUrl, updateFieldUrl, getTypesUrl, websocketsConfig } =
        useAssistantStore();
    const { getToken } = useTokenStore();

    const messages = ref([]);
    const threadId = ref(null);
    const isSending = ref(false);
    const wordQueue = ref([]);
    const isTyping = ref(false);
    const activeWidget = ref(null);
    const typeSelectorShown = ref(false);

    // File upload integration
    const fileUpload = useFileUpload();

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
                // Show pending widget if any
                if (messages.value[messageIndex].pendingWidget) {
                    activeWidget.value = messages.value[messageIndex].pendingWidget;
                    delete messages.value[messageIndex].pendingWidget;
                }
                // Enable file attachments if pending
                if (messages.value[messageIndex].pendingFileAttachments) {
                    fileUpload.enableAttachments();
                    delete messages.value[messageIndex].pendingFileAttachments;
                }
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
            // Show pending widget if any
            if (messages.value[messageIndex].pendingWidget) {
                activeWidget.value = messages.value[messageIndex].pendingWidget;
                delete messages.value[messageIndex].pendingWidget;
            }
            // Enable file attachments if pending
            if (messages.value[messageIndex].pendingFileAttachments) {
                fileUpload.enableAttachments();
                delete messages.value[messageIndex].pendingFileAttachments;
            }
        } else if (isComplete) {
            messages.value[messageIndex].shouldComplete = true;
        }
    };

    const sendMessage = async (content) => {
        if (!content || !content.trim() || isSending.value || isAssistantResponding.value) return;

        // Wait for all uploads to complete before sending
        if (!fileUpload.allUploadsComplete.value) {
            return;
        }

        isSending.value = true;

        addUserMessage(content);

        const payload = { content, thread_id: threadId.value };

        // Include file URLs if any files were uploaded
        const fileUrls = fileUpload.getCompletedFileUrls();
        if (fileUrls.length > 0) {
            payload.file_urls = fileUrls;
        }

        try {
            const response = await axios.post(assistantSendMessageUrl, payload);
            if (response.data.thread_id) {
                threadId.value = response.data.thread_id;
            }
            addAssistantMessage();
        } catch (error) {
            addAssistantMessage();
            updateAssistantMessage('', true, 'Failed to send message.');
        } finally {
            isSending.value = false;
            // Clear files and disable attachments after sending
            fileUpload.disableAttachments();
        }
    };

    const connection = useAssistantConnection(websocketsConfig, getToken);

    const handleActionRequest = (actionType, params) => {
        // Don't show widgets or enable attachments until assistant response is complete
        const messageIndex = messages.value.findIndex(
            (message) => message.author === 'assistant' && !message.isComplete,
        );

        if (actionType === 'enable_file_attachments') {
            if (messageIndex !== -1) {
                // Store action to execute after response completes
                messages.value[messageIndex].pendingFileAttachments = true;
            } else {
                // Response already complete, enable immediately
                fileUpload.enableAttachments();
            }
            return;
        }

        // Track when type selector is shown (hides "New Request" link)
        if (actionType === 'select_service_request_type') {
            typeSelectorShown.value = true;
        }

        if (messageIndex !== -1) {
            // Store the widget to show after response completes
            messages.value[messageIndex].pendingWidget = { type: actionType, params };
        } else {
            // Response already complete, show immediately
            activeWidget.value = { type: actionType, params };
        }
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
        const { type, field_id, type_id, priority_id, value, display_text } = submitData;

        if (isSending.value || isAssistantResponding.value) return;
        isSending.value = true;

        addUserMessage(display_text || 'Submitted');

        try {
            let endpoint;
            let payload = { thread_id: threadId.value, message: display_text || 'Submitted' };

            if (type === 'type_selection') {
                endpoint = selectTypeUrl;
                payload.priority_id = priority_id;
            } else if (type === 'field_response') {
                endpoint = updateFieldUrl;
                payload.field_id = field_id;
                payload.value = value;
            }

            const response = await axios.post(endpoint, payload);
            if (response.data.thread_id) {
                threadId.value = response.data.thread_id;
            }
            addAssistantMessage();
        } catch (error) {
            console.error('[Assistant] Widget submission failed:', error);
            if (error.response?.data?.message) {
                console.error('[Assistant] Server error:', error.response.data);
            }
            addAssistantMessage();
            updateAssistantMessage('', true, 'Failed to submit widget data.');
        } finally {
            isSending.value = false;
        }

        activeWidget.value = null;
    };

    const handleWidgetCancel = async () => {
        activeWidget.value = null;
    };

    const showNewRequestSelector = async () => {
        if (!getTypesUrl || isSending.value || isAssistantResponding.value) return;

        try {
            const response = await axios.get(getTypesUrl);
            const typesTree = response.data.types_tree || [];

            if (typesTree.length === 0) {
                console.error('[Assistant] No service request types available');
                return;
            }

            typeSelectorShown.value = true;
            activeWidget.value = {
                type: 'select_service_request_type',
                params: {
                    suggestion: null,
                    types_tree: typesTree,
                },
            };
        } catch (error) {
            console.error('[Assistant] Failed to fetch service request types:', error);
        }
    };

    const showNewRequestLink = computed(() => {
        return getTypesUrl && !typeSelectorShown.value;
    });

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
        // New request link
        showNewRequestLink,
        showNewRequestSelector,
        // File upload exports
        fileAttachments: fileUpload.files,
        fileAttachmentsEnabled: fileUpload.isEnabled,
        addFileAttachments: fileUpload.addFiles,
        removeFileAttachment: fileUpload.removeFile,
        allUploadsComplete: fileUpload.allUploadsComplete,
    };
}
