<script setup>
    import { ChatBubbleLeftEllipsisIcon } from '@heroicons/vue/16/solid';
    import { useMarkdown } from '../../Composables/assistant/useMarkdown.js';

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
                    class="prose prose-sm max-w-none text-gray-800 leading-relaxed prose-p:my-0 prose-p:first:mt-0 prose-p:last:mb-0 prose-ul:my-1 prose-ol:my-1 prose-li:my-0"
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
