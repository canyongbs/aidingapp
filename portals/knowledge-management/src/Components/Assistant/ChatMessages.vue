<script setup>
    import { ChatBubbleLeftEllipsisIcon } from '@heroicons/vue/16/solid';
    import { nextTick, onMounted, onUnmounted, ref, watch } from 'vue';
    import ChatMessage from './ChatMessage.vue';

    const props = defineProps({
        messages: { type: Array, required: true },
        welcomeMessage: { type: String, required: true },
        isOpen: { type: Boolean, default: false },
    });

    const messagesContainer = ref(null);
    const autoScroll = ref(true);

    const isNearBottom = (el) => {
        if (!el) return true;
        const distanceFromBottom = el.scrollHeight - (el.scrollTop + el.clientHeight);
        return distanceFromBottom <= 20;
    };

    const scrollToBottom = (force = false) => {
        if (!messagesContainer.value) return;
        if (!force && !autoScroll.value) return;

        nextTick(() => {
            requestAnimationFrame(() => {
                if (messagesContainer.value) {
                    messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
                }
            });
        });
    };

    const handleMessagesScroll = () => {
        if (!messagesContainer.value) return;
        autoScroll.value = isNearBottom(messagesContainer.value);
    };

    onMounted(() => {
        if (props.isOpen && messagesContainer.value) {
            autoScroll.value = isNearBottom(messagesContainer.value);
            messagesContainer.value.addEventListener('scroll', handleMessagesScroll, { passive: true });
        }
    });

    onUnmounted(() => {
        if (messagesContainer.value) {
            messagesContainer.value.removeEventListener('scroll', handleMessagesScroll);
        }
    });

    watch(
        () => props.isOpen,
        (open) => {
            nextTick(() => {
                if (open && messagesContainer.value) {
                    autoScroll.value = isNearBottom(messagesContainer.value);
                    messagesContainer.value.addEventListener('scroll', handleMessagesScroll, { passive: true });
                    scrollToBottom(true);
                } else if (!open && messagesContainer.value) {
                    messagesContainer.value.removeEventListener('scroll', handleMessagesScroll);
                }
            });
        },
    );

    watch(
        () => props.messages,
        () => {
            scrollToBottom();
        },
        { deep: true },
    );
</script>

<template>
    <div class="flex-1 overflow-y-auto p-6 bg-gradient-to-b from-gray-50 to-white" ref="messagesContainer">
        <div class="flex gap-3 mb-4">
            <div class="shrink-0">
                <div class="w-8 h-8 rounded-full bg-brand-100 flex items-center justify-center">
                    <ChatBubbleLeftEllipsisIcon class="w-4 h-4 text-brand-600" />
                </div>
            </div>
            <div class="flex-1">
                <div class="bg-white rounded-lg rounded-tl-sm px-4 py-3 shadow-sm border border-gray-200">
                    <p class="text-sm text-gray-800 leading-relaxed">{{ props.welcomeMessage }}</p>
                </div>
            </div>
        </div>

        <ChatMessage v-for="(chatMessage, index) in props.messages" :key="index" :message="chatMessage" />
    </div>
</template>
