<script setup>
    import { ChatBubbleLeftRightIcon, XMarkIcon } from '@heroicons/vue/24/outline';

    defineEmits(['close']);
</script>

<template>
    <div
        class="bg-gradient-to-r from-brand-600 to-brand-700 text-white px-6 py-4 flex items-center justify-between shadow-md shrink-0"
    >
        <div class="flex items-center gap-3">
            <div class="bg-white/20 p-2 rounded-lg">
                <ChatBubbleLeftRightIcon class="w-5 h-5" />
            </div>
            <h2 class="text-lg font-semibold tracking-tight">Support Assistant</h2>
        </div>
        <button
            @click="$emit('close')"
            class="text-white/90 hover:text-white hover:bg-white/10 transition-all rounded-lg p-1.5"
            aria-label="Close chat"
        >
            <XMarkIcon class="w-5 h-5" />
        </button>
    </div>
</template>
