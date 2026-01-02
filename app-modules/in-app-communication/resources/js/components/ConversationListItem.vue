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
    import { HashtagIcon, LockClosedIcon } from '@heroicons/vue/24/outline';
    import { MapPinIcon } from '@heroicons/vue/24/solid';
    import { computed, ref } from 'vue';
    import { extractTextFromTipTap } from '../utils/helpers';
    import Avatar from './ui/Avatar.vue';

    const props = defineProps({
        conversation: { type: Object, required: true },
        isSelected: { type: Boolean, default: false },
        unreadCount: { type: Number, default: 0 },
        currentUserId: { type: String, required: true },
    });

    const emit = defineEmits(['click', 'pin']);

    const isHovered = ref(false);
    const isPinning = ref(false);

    async function handlePin(event) {
        event.stopPropagation();
        if (isPinning.value) return;
        isPinning.value = true;
        try {
            await emit('pin', props.conversation.id);
        } finally {
            isPinning.value = false;
        }
    }

    const displayName = computed(() => {
        return props.conversation.display_name || 'Unknown';
    });

    const avatarUrl = computed(() => {
        return props.conversation.avatar_url || null;
    });

    const lastMessagePreview = computed(() => {
        if (!props.conversation.last_message) {
            return 'No messages yet';
        }

        const content = props.conversation.last_message.content;

        // Handle TipTap JSON format
        if (content && typeof content === 'object') {
            const text = extractTextFromTipTap(content).trim();
            if (text) {
                return text.length > 50 ? text.substring(0, 50) + '...' : text;
            }
            return 'Message';
        }

        // Handle plain HTML string
        if (typeof content === 'string') {
            const stripped = content.replace(/<[^>]*>/g, '');
            return stripped.length > 50 ? stripped.substring(0, 50) + '...' : stripped;
        }

        return 'Message';
    });

    const timeAgo = computed(() => {
        if (!props.conversation.last_message?.created_at) {
            return '';
        }

        const date = new Date(props.conversation.last_message.created_at);
        const now = new Date();
        const diffMs = now - date;
        const diffMins = Math.floor(diffMs / 60000);
        const diffHours = Math.floor(diffMs / 3600000);
        const diffDays = Math.floor(diffMs / 86400000);

        if (diffMins < 1) return 'now';
        if (diffMins < 60) return `${diffMins}m`;
        if (diffHours < 24) return `${diffHours}h`;
        if (diffDays < 7) return `${diffDays}d`;

        return date.toLocaleDateString();
    });
</script>

<template>
    <button
        type="button"
        class="group w-full px-4 py-3 text-left transition-all duration-150"
        :class="[
            isSelected
                ? 'bg-primary-50 dark:bg-primary-900/20 border-l-2 border-primary-600'
                : 'hover:bg-gray-50 dark:hover:bg-gray-800/50 border-l-2 border-transparent',
        ]"
        @click="$emit('click')"
        @mouseenter="isHovered = true"
        @mouseleave="isHovered = false"
    >
        <div class="flex items-center gap-3">
            <!-- Avatar -->
            <div class="relative flex-shrink-0">
                <template v-if="conversation.type === 'channel'">
                    <div
                        class="relative flex h-10 w-10 items-center justify-center rounded-lg bg-primary-100 dark:bg-primary-900/30"
                    >
                        <HashtagIcon class="h-5 w-5 text-primary-600 dark:text-primary-400" />
                        <LockClosedIcon
                            v-if="conversation.is_private"
                            class="absolute -bottom-0.5 -right-0.5 h-3.5 w-3.5 text-gray-500 dark:text-gray-400"
                        />
                    </div>
                </template>
                <template v-else>
                    <Avatar :src="avatarUrl" :name="displayName" size="md" ring />
                </template>

                <!-- Unread Badge -->
                <span
                    v-if="unreadCount > 0"
                    class="absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-primary-600 px-1.5 text-xs font-medium text-white shadow-sm"
                >
                    {{ unreadCount > 99 ? '99+' : unreadCount }}
                </span>
            </div>

            <!-- Content -->
            <div class="min-w-0 flex-1">
                <div class="flex items-center justify-between">
                    <span
                        class="truncate font-medium"
                        :class="[
                            unreadCount > 0 ? 'text-gray-900 dark:text-white' : 'text-gray-700 dark:text-gray-300',
                        ]"
                    >
                        {{ displayName }}
                    </span>
                    <div class="ml-2 flex items-center gap-1.5 flex-shrink-0">
                        <!-- Pin Button -->
                        <button
                            v-if="isHovered || conversation.is_pinned"
                            type="button"
                            class="p-0.5 rounded transition-colors"
                            :class="[
                                conversation.is_pinned
                                    ? 'text-primary-600 dark:text-primary-400'
                                    : 'text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 opacity-0 group-hover:opacity-100',
                            ]"
                            :title="conversation.is_pinned ? 'Unpin conversation' : 'Pin conversation'"
                            :disabled="isPinning"
                            @click="handlePin"
                        >
                            <MapPinIcon class="w-3.5 h-3.5" />
                        </button>
                        <span class="text-xs text-gray-500 dark:text-gray-500">
                            {{ timeAgo }}
                        </span>
                    </div>
                </div>
                <p
                    class="mt-0.5 truncate text-sm leading-relaxed"
                    :class="[
                        unreadCount > 0
                            ? 'font-medium text-gray-700 dark:text-gray-300'
                            : 'text-gray-500 dark:text-gray-400',
                    ]"
                >
                    {{ lastMessagePreview }}
                </p>
            </div>
        </div>
    </button>
</template>
