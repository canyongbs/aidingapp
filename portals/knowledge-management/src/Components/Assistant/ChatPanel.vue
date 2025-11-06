<script setup>
    import { computed } from 'vue';
    import { useAssistantChat } from '../../Composables/assistant/useAssistantChat.js';
    import { useAuthStore } from '../../Stores/auth.js';
    import ChatHeader from './ChatHeader.vue';
    import ChatInput from './ChatInput.vue';
    import ChatMessages from './ChatMessages.vue';

    const props = defineProps({
        isOpen: { type: Boolean, default: false },
    });

    const emit = defineEmits(['close']);

    const { messages, isSending, isAssistantResponding, sendMessage } = useAssistantChat();

    const authStore = useAuthStore();
    const welcomeMessage = computed(() => {
        return `Hi ${authStore.user?.first_name || 'there'}, I am your support assistant. I can help you find information and troubleshoot issues. How can I assist you today?`;
    });
</script>

<template>
    <Transition
        enter-active-class="transition-all duration-100 ease-out"
        enter-from-class="opacity-0 scale-95 translate-y-4"
        enter-to-class="opacity-100 scale-100 translate-y-0"
        leave-active-class="transition-all duration-75 ease-in"
        leave-from-class="opacity-100 scale-100 translate-y-0"
        leave-to-class="opacity-0 scale-95 translate-y-4"
    >
        <div
            v-if="props.isOpen"
            class="mb-4 w-[400px] max-w-full h-[650px] max-h-full bg-white rounded-lg shadow-2xl flex flex-col overflow-hidden ring-1 ring-brand-950/5 backdrop-blur-sm origin-bottom-right"
        >
            <ChatHeader @close="emit('close')" />

            <ChatMessages :messages="messages" :welcome-message="welcomeMessage" :is-open="props.isOpen" />

            <ChatInput :disabled="isSending || isAssistantResponding" @send="sendMessage" />
        </div>
    </Transition>
</template>
