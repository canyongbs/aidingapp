<!--
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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
    import { ChatBubbleLeftEllipsisIcon } from '@heroicons/vue/24/outline';
    import { nextTick, onMounted, ref, watch } from 'vue';
    import MessageBubble from './MessageBubble.vue';
    import TypingIndicator from './TypingIndicator.vue';
    import LoadingSpinner from './ui/LoadingSpinner.vue';

    const props = defineProps({
        messages: { type: Array, required: true },
        loading: { type: Boolean, default: false },
        hasMore: { type: Boolean, default: false },
        currentUserId: { type: String, required: true },
        typingUsers: { type: Array, default: () => [] },
        conversation: { type: Object, required: true },
    });

    const emit = defineEmits(['load-more']);

    const containerRef = ref(null);
    const isAtBottom = ref(true);
    const isLoadingMore = ref(false);
    const isInitialLoad = ref(true);
    const previousScrollHeight = ref(0);

    const MESSAGE_GROUP_THRESHOLD_MS = 5 * 60 * 1000; // 5 minutes

    function isWithinGroupThreshold(message, prevMessage) {
        if (!prevMessage) return false;

        const currTime = new Date(message.created_at).getTime();
        const prevTime = new Date(prevMessage.created_at).getTime();

        return currTime - prevTime < MESSAGE_GROUP_THRESHOLD_MS;
    }

    function shouldShowAuthor(message, index) {
        if (props.conversation.type === 'direct') {
            return false;
        }

        if (index === 0) return true;

        const prevMessage = props.messages[index - 1];
        return prevMessage.author_id !== message.author_id;
    }

    function isGroupedWithPrevious(message, index) {
        if (index === 0) return false;

        const prevMessage = props.messages[index - 1];

        // Must be same author
        if (prevMessage.author_id !== message.author_id) return false;

        // Must be within 5 minutes
        return isWithinGroupThreshold(message, prevMessage);
    }

    function isGroupedWithNext(message, index) {
        if (index === props.messages.length - 1) return false;

        const nextMessage = props.messages[index + 1];

        // Must be same author
        if (nextMessage.author_id !== message.author_id) return false;

        // Must be within 5 minutes
        return isWithinGroupThreshold(nextMessage, message);
    }

    function isLastInGroup(message, index) {
        return !isGroupedWithNext(message, index);
    }

    function scrollToBottom(smooth = false) {
        nextTick(() => {
            if (containerRef.value) {
                containerRef.value.scrollTo({
                    top: containerRef.value.scrollHeight,
                    behavior: smooth ? 'smooth' : 'auto',
                });
            }
        });
    }

    function handleScroll() {
        if (!containerRef.value) return;

        const { scrollTop, scrollHeight, clientHeight } = containerRef.value;
        isAtBottom.value = scrollHeight - scrollTop - clientHeight < 50;

        // Trigger load more when within 500px of top for seamless loading
        if (scrollTop < 500 && props.hasMore && !props.loading && !isLoadingMore.value) {
            isLoadingMore.value = true;
            previousScrollHeight.value = containerRef.value.scrollHeight;
            emit('load-more');
        }
    }

    // Watch for messages changes
    watch(
        () => props.messages,
        (newMessages, oldMessages) => {
            if (!containerRef.value) return;

            nextTick(() => {
                if (isLoadingMore.value) {
                    // Preserve scroll position when loading older messages
                    const newScrollHeight = containerRef.value.scrollHeight;
                    const scrollDiff = newScrollHeight - previousScrollHeight.value;
                    containerRef.value.scrollTop = scrollDiff;
                    isLoadingMore.value = false;
                } else if (isInitialLoad.value && newMessages.length > 0) {
                    // Scroll to bottom on initial load
                    scrollToBottom();
                    isInitialLoad.value = false;
                } else if (!oldMessages || newMessages.length > oldMessages.length) {
                    // New message added - scroll to bottom if already at bottom
                    if (isAtBottom.value) {
                        scrollToBottom(true);
                    }
                }
            });
        },
        { deep: false },
    );

    // Reset state when conversation changes
    watch(
        () => props.conversation.id,
        () => {
            isLoadingMore.value = false;
            isInitialLoad.value = true;
            isAtBottom.value = true;
        },
    );

    onMounted(() => {
        if (props.messages.length > 0) {
            scrollToBottom();
            isInitialLoad.value = false;
        }
    });
</script>

<template>
    <div
        ref="containerRef"
        class="flex-1 overflow-y-auto p-6 bg-gradient-to-b from-gray-50 to-white dark:from-gray-800 dark:to-gray-900"
        @scroll="handleScroll"
    >
        <!-- Load More Spinner -->
        <div v-if="loading && hasMore" class="mb-4 flex justify-center py-2">
            <LoadingSpinner label="Loading messages..." />
        </div>

        <!-- Empty State -->
        <div v-if="messages.length === 0 && !loading" class="flex h-full items-center justify-center">
            <div class="text-center">
                <div
                    class="mx-auto w-12 h-12 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center mb-3"
                >
                    <ChatBubbleLeftEllipsisIcon class="w-6 h-6 text-primary-600 dark:text-primary-400" />
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400">No messages yet. Start the conversation!</p>
            </div>
        </div>

        <!-- Messages -->
        <div>
            <MessageBubble
                v-for="(message, index) in messages"
                :key="message.id"
                :message="message"
                :is-own="message.author_id === currentUserId"
                :show-author="shouldShowAuthor(message, index)"
                :is-grouped="isGroupedWithPrevious(message, index)"
                :show-timestamp="isLastInGroup(message, index)"
                :current-user-id="currentUserId"
            />
        </div>

        <!-- Typing Indicator -->
        <TypingIndicator
            v-if="typingUsers.length > 0"
            :typing-users="typingUsers"
            :conversation="conversation"
            :current-user-id="currentUserId"
        />
    </div>
</template>
