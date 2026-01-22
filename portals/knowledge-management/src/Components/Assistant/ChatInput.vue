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
    import { PaperAirplaneIcon } from '@heroicons/vue/24/outline';
    import { nextTick, ref } from 'vue';

    const emit = defineEmits(['send', 'addFiles', 'removeFile']);

    const props = defineProps({
        disabled: { type: Boolean, default: false },
    });

    const textarea = ref(null);
    const fileInput = ref(null);
    const localMessage = ref('');

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
        <div class="flex items-end gap-2">
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
                :disabled="props.disabled || !localMessage.trim()"
            >
                <PaperAirplaneIcon class="w-5 h-5" />
            </button>
        </div>
    </div>
</template>
