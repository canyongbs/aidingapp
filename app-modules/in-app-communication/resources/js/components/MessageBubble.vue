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
    import { ArrowPathIcon, XMarkIcon } from '@heroicons/vue/20/solid';
    import Mention from '@tiptap/extension-mention';
    import { generateHTML } from '@tiptap/html';
    import StarterKit from '@tiptap/starter-kit';
    import DOMPurify from 'dompurify';
    import { computed } from 'vue';
    import { SafeLink } from '../extensions/SafeLink';
    import { cleanTipTapContent } from '../utils/helpers';
    import { mentionClasses } from '../utils/mention-classes';
    import Avatar from './ui/Avatar.vue';

    const props = defineProps({
        message: { type: Object, required: true },
        isOwn: { type: Boolean, default: false },
        showAuthor: { type: Boolean, default: true },
        isGrouped: { type: Boolean, default: false },
        showTimestamp: { type: Boolean, default: true },
        currentUserId: { type: String, default: null },
    });

    const emit = defineEmits(['retry', 'dismiss']);

    const sanitizedContent = computed(() => {
        const content = props.message.content;

        // Handle TipTap JSON format
        if (content && typeof content === 'object') {
            try {
                // Clean the content to remove invalid nodes before rendering
                const cleanedContent = cleanTipTapContent(JSON.parse(JSON.stringify(content)));
                const html = generateHTML(cleanedContent, [
                    StarterKit,
                    SafeLink.configure({
                        openOnClick: false,
                        HTMLAttributes: {
                            class: 'text-primary-600 dark:text-primary-400 hover:underline cursor-pointer',
                        },
                    }),
                    Mention.configure({
                        HTMLAttributes: {
                            class: 'mention',
                        },
                        renderText({ node }) {
                            return `@${node.attrs.label ?? node.attrs.id}`;
                        },
                    }),
                ]);

                // Post-process to style mentions based on current user
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = html;

                tempDiv.querySelectorAll('[data-type="mention"]').forEach((el) => {
                    const mentionId = el.getAttribute('data-id');
                    el.className =
                        mentionId === props.currentUserId ? mentionClasses.currentUser : mentionClasses.otherUser;
                });

                return DOMPurify.sanitize(tempDiv.innerHTML);
            } catch (e) {
                console.error('Error generating HTML:', e);
                return '';
            }
        }

        // Fallback for plain HTML string
        return DOMPurify.sanitize(content || '');
    });

    const formattedTime = computed(() => {
        const date = new Date(props.message.created_at);
        return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    });

    function handleContentClick(event) {
        const target = event.target.closest('[data-safe-link]');
        if (target && target.getAttribute('href')) {
            const href = target.getAttribute('href');
            if (href && !href.startsWith('javascript:')) {
                window.open(href, '_blank', 'noopener,noreferrer');
            }
        }
    }
</script>

<template>
    <div class="flex gap-3" :class="[isOwn ? 'justify-end' : '', showTimestamp ? 'mb-4' : 'mb-1']">
        <!-- Avatar (for received messages) -->
        <div v-if="!isOwn" class="shrink-0 w-8">
            <Avatar
                v-if="!isGrouped"
                :src="message.author_avatar"
                :name="message.author_name || 'Unknown'"
                size="sm"
                ring
            />
            <!-- Empty space to maintain alignment when grouped -->
        </div>

        <!-- Message Content -->
        <div class="flex-1 max-w-[80%]" :class="isOwn ? 'flex flex-col items-end' : ''">
            <!-- Author Name -->
            <p
                v-if="showAuthor && !isOwn && !isGrouped"
                class="mb-1 text-xs font-medium text-gray-600 dark:text-gray-400"
            >
                {{ message.author_name || 'Unknown' }}
            </p>

            <!-- Bubble -->
            <div
                class="px-4 py-2 shadow-sm border w-fit max-w-full"
                :class="[
                    isOwn
                        ? 'bg-primary-50 dark:bg-primary-900/30 border-primary-200 dark:border-primary-800 rounded-lg rounded-tr-sm'
                        : 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 rounded-lg rounded-tl-sm',
                    message._sending ? 'opacity-70' : '',
                    message._failed ? 'border-red-300 dark:border-red-700' : '',
                ]"
            >
                <div
                    class="prose prose-sm max-w-none leading-relaxed prose-p:my-0 prose-p:first:mt-0 prose-p:last:mb-0 prose-ul:my-1 prose-ol:my-1 prose-li:my-0 dark:prose-invert"
                    :class="[isOwn ? 'text-gray-800 dark:text-gray-200' : 'text-gray-800 dark:text-gray-200']"
                    v-html="sanitizedContent"
                    @click="handleContentClick"
                />
            </div>

            <!-- Timestamp / Status -->
            <div v-if="showTimestamp || message._sending || message._failed" class="mt-0.5 text-xs">
                <template v-if="message._failed">
                    <span class="text-red-500 dark:text-red-400">Failed to send</span>
                    <span class="mx-1 text-gray-300 dark:text-gray-600">·</span>
                    <button
                        type="button"
                        class="inline-flex items-center gap-0.5 text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300"
                        @click="emit('retry', message.id)"
                    >
                        <ArrowPathIcon class="w-3 h-3" />
                        Retry
                    </button>
                    <span class="mx-1 text-gray-300 dark:text-gray-600">·</span>
                    <button
                        type="button"
                        class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300"
                        @click="emit('dismiss', message.id)"
                    >
                        <XMarkIcon class="w-3 h-3 inline" />
                        Dismiss
                    </button>
                </template>
                <span v-else-if="message._sending" class="text-gray-400 dark:text-gray-500">Sending...</span>
                <span v-else class="text-gray-500 dark:text-gray-500">{{ formattedTime }}</span>
            </div>
        </div>
    </div>
</template>
