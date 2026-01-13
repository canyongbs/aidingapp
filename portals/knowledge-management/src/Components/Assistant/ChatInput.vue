<!--
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
-->
<script setup>
    import { computed, nextTick, ref } from 'vue';

    const emit = defineEmits(['send', 'addFiles', 'removeFile']);

    const props = defineProps({
        disabled: { type: Boolean, default: false },
        attachmentsEnabled: { type: Boolean, default: false },
        fileAttachments: { type: Array, default: () => [] },
        allUploadsComplete: { type: Boolean, default: true },
    });

    const textarea = ref(null);
    const fileInput = ref(null);
    const localMessage = ref('');

    const hasFiles = computed(() => props.fileAttachments.length > 0);
    const hasPendingUploads = computed(() => !props.allUploadsComplete);

    const adjustTextareaHeight = () => {
        if (!textarea.value) return;
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

    const send = () => {
        const content = localMessage.value;
        if (!content || !content.trim()) return;
        if (hasPendingUploads.value) return;
        emit('send', content);
        localMessage.value = '';
        nextTick(() => adjustTextareaHeight());
    };

    const triggerFileInput = () => {
        fileInput.value?.click();
    };

    const handleFileSelect = (event) => {
        const files = event.target.files;
        if (files && files.length > 0) {
            emit('addFiles', Array.from(files));
        }
        // Reset input so same file can be selected again
        event.target.value = '';
    };

    const removeFile = (fileId) => {
        emit('removeFile', fileId);
    };

    const getStatusColor = (status) => {
        switch (status) {
            case 'complete':
                return 'bg-green-100 text-green-800';
            case 'error':
                return 'bg-red-100 text-red-800';
            case 'uploading':
                return 'bg-blue-100 text-blue-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    };

    const truncateFilename = (name, maxLength = 20) => {
        if (name.length <= maxLength) return name;
        const ext = name.split('.').pop();
        const base = name.slice(0, -(ext.length + 1));
        const truncatedBase = base.slice(0, maxLength - ext.length - 4) + '...';
        return `${truncatedBase}.${ext}`;
    };
</script>

<template>
    <div class="border-t border-gray-200/80 bg-white p-4 shadow-lg shrink-0">
        <!-- File attachments display -->
        <div v-if="hasFiles" class="mb-3 flex flex-wrap gap-2">
            <div
                v-for="file in props.fileAttachments"
                :key="file.id"
                :class="[
                    'inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-medium transition-all',
                    getStatusColor(file.status),
                ]"
            >
                <!-- File icon -->
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    class="w-4 h-4 shrink-0"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13"
                    />
                </svg>

                <span :title="file.originalName">{{ truncateFilename(file.originalName) }}</span>

                <!-- Progress indicator for uploading files -->
                <span v-if="file.status === 'uploading'" class="text-xs opacity-75"> {{ file.progress }}% </span>

                <!-- Error indicator -->
                <span v-if="file.status === 'error'" class="text-xs"> Failed </span>

                <!-- Remove button -->
                <button
                    @click="removeFile(file.id)"
                    class="ml-1 hover:bg-black/10 rounded-full p-0.5 transition-colors"
                    aria-label="Remove file"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="2"
                        stroke="currentColor"
                        class="w-3 h-3"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <div class="flex items-end gap-2">
            <!-- Hidden file input -->
            <input
                ref="fileInput"
                type="file"
                multiple
                class="hidden"
                @change="handleFileSelect"
                accept="image/*,.pdf,.doc,.docx,.txt,.csv,.xls,.xlsx"
            />

            <!-- Attachment button (shown when enabled) -->
            <button
                v-if="props.attachmentsEnabled"
                @click="triggerFileInput"
                class="text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg p-3 transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-brand-500 shrink-0"
                aria-label="Attach files"
                title="Attach files"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    class="w-5 h-5"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13"
                    />
                </svg>
            </button>

            <textarea
                ref="textarea"
                v-model="localMessage"
                @input="handleInput"
                placeholder="Type your message..."
                rows="1"
                class="flex-1 resize-none rounded-lg border border-gray-300 px-4 py-3 text-sm leading-5 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent placeholder:text-gray-400 overflow-y-auto"
                style="height: 44px"
                @keydown.enter.exact.prevent="send"
            ></textarea>

            <button
                class="bg-brand-600 hover:bg-brand-700 active:bg-brand-800 text-white rounded-lg p-3 font-medium transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 shadow-sm hover:shadow disabled:opacity-50 disabled:cursor-not-allowed shrink-0"
                aria-label="Send message"
                @click="send"
                :disabled="props.disabled || !localMessage.trim() || hasPendingUploads"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    class="w-5 h-5"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.768 59.768 0 013.27 20.876L6 12zm0 0h7.5"
                    />
                </svg>
            </button>
        </div>
    </div>
</template>
