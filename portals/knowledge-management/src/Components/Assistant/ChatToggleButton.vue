<script setup>
    import { ChatBubbleLeftRightIcon, ChevronDownIcon } from '@heroicons/vue/24/outline';

    const props = defineProps({
        isOpen: { type: Boolean, default: false },
    });

    const emit = defineEmits(['toggle']);
</script>

<template>
    <button
        @click="emit('toggle')"
        class="bg-gradient-to-br from-brand-600 to-brand-700 hover:from-brand-700 hover:to-brand-800 text-white rounded-full p-4 shadow-xl hover:shadow-2xl transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-brand-500/50 hover:scale-105 active:scale-95"
        aria-label="Toggle chat assistant"
    >
        <ChatBubbleLeftRightIcon v-if="!props.isOpen" class="w-6 h-6" />
        <ChevronDownIcon v-else class="w-6 h-6" />
    </button>
</template>
