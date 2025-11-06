<script setup>
    import { nextTick, ref } from 'vue';

    const emit = defineEmits(['send']);

    const props = defineProps({
        disabled: { type: Boolean, default: false },
    });

    const textarea = ref(null);
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
