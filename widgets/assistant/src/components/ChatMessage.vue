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
    import { ChatBubbleLeftEllipsisIcon } from '@heroicons/vue/16/solid';
    import { useMarkdown } from '../composables/useMarkdown.js';

    const props = defineProps({
        message: { type: Object, required: true },
    });

    const { renderMarkdown } = useMarkdown();
</script>

<template>
    <div class="flex gap-3 mb-4" :class="props.message.author === 'user' ? 'justify-end' : ''">
        <div v-if="props.message.author === 'assistant'" class="shrink-0">
            <div class="w-8 h-8 rounded-full bg-brand-100 flex items-center justify-center">
                <ChatBubbleLeftEllipsisIcon class="w-4 h-4 text-brand-600" />
            </div>
        </div>

        <div class="flex-1 max-w-[80%]">
            <div
                :class="
                    props.message.author === 'assistant'
                        ? 'bg-white rounded-lg rounded-tl-sm'
                        : 'bg-brand-50 rounded-lg rounded-tr-sm'
                "
                class="px-4 py-3 shadow-sm border border-gray-200"
            >
                <div
                    v-if="props.message.author === 'assistant'"
                    class="prose prose-sm max-w-none text-sm text-gray-800 leading-relaxed prose-p:my-0 prose-p:first:mt-0 prose-p:last:mb-0 prose-ul:my-1 prose-ol:my-1 prose-li:my-0"
                    v-html="renderMarkdown(props.message.content)"
                ></div>
                <p v-else class="text-sm text-gray-800 leading-relaxed whitespace-pre-line">
                    {{ props.message.content }}
                </p>

                <div v-if="props.message.error" class="text-xs text-red-500 mt-1">
                    {{ props.message.error }}
                </div>

                <div
                    v-if="props.message.author === 'assistant' && !props.message.content && !props.message.isComplete"
                    class="flex items-center space-x-1"
                >
                    <div class="w-1.5 h-1.5 bg-brand-400 rounded-full animate-bounce"></div>
                    <div class="w-1.5 h-1.5 bg-brand-400 rounded-full animate-bounce [animation-delay:0.2s]"></div>
                    <div class="w-1.5 h-1.5 bg-brand-400 rounded-full animate-bounce [animation-delay:0.4s]"></div>
                </div>
            </div>
        </div>
    </div>
</template>
