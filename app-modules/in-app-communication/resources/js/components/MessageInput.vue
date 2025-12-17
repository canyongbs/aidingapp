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
    import { ArrowPathIcon, PaperAirplaneIcon } from '@heroicons/vue/24/solid';
    import Mention from '@tiptap/extension-mention';
    import Placeholder from '@tiptap/extension-placeholder';
    import StarterKit from '@tiptap/starter-kit';
    import { EditorContent, useEditor, VueRenderer } from '@tiptap/vue-3';
    import tippy from 'tippy.js';
    import { computed, onBeforeUnmount, ref, watch } from 'vue';
    import { cleanTipTapContent } from '../utils/helpers';
    import { mentionClasses } from '../utils/mention-classes';
    import MentionList from './MentionList.vue';

    const props = defineProps({
        disabled: { type: Boolean, default: false },
        placeholder: { type: String, default: 'Type your message...' },
        participants: { type: Array, default: () => [] },
        currentUserId: { type: String, default: null },
    });

    const emit = defineEmits(['send', 'typing']);

    const isSending = ref(false);

    // Filter out current user from mention suggestions
    const mentionableParticipants = computed(() => {
        return props.participants
            .filter((participant) => participant.participant_id !== props.currentUserId && participant.participant)
            .map((participant) => ({
                id: participant.participant_id,
                name: participant.participant.name,
                avatar_url: participant.participant.avatar_url,
            }));
    });

    const editor = useEditor({
        extensions: [
            StarterKit.configure({
                heading: false,
                codeBlock: false,
                blockquote: false,
                horizontalRule: false,
            }),
            Placeholder.configure({
                placeholder: props.placeholder,
            }),
            Mention.configure({
                HTMLAttributes: {
                    class: mentionClasses.input,
                },
                suggestion: {
                    items: ({ query }) => {
                        return mentionableParticipants.value
                            .filter((item) => item.name.toLowerCase().includes(query.toLowerCase()))
                            .slice(0, 5);
                    },
                    render: () => {
                        let component;
                        let popup;

                        return {
                            onStart: (props) => {
                                component = new VueRenderer(MentionList, {
                                    props,
                                    editor: props.editor,
                                });

                                if (!props.clientRect) {
                                    return;
                                }

                                popup = tippy('body', {
                                    getReferenceClientRect: props.clientRect,
                                    appendTo: () => document.body,
                                    content: component.element,
                                    showOnCreate: true,
                                    interactive: true,
                                    trigger: 'manual',
                                    placement: 'top-start',
                                    arrow: false,
                                    onShow(instance) {
                                        // Clear tippy's default styling
                                        const box = instance.popper.querySelector('.tippy-box');
                                        const content = instance.popper.querySelector('.tippy-content');
                                        if (box) {
                                            box.style.background = 'transparent';
                                            box.style.boxShadow = 'none';
                                            box.style.borderRadius = '0';
                                        }
                                        if (content) {
                                            content.style.padding = '0';
                                        }
                                    },
                                });
                            },

                            onUpdate(props) {
                                component.updateProps(props);

                                if (!props.clientRect) {
                                    return;
                                }

                                popup[0].setProps({
                                    getReferenceClientRect: props.clientRect,
                                });
                            },

                            onKeyDown(props) {
                                if (props.event.key === 'Escape') {
                                    popup[0].hide();
                                    return true;
                                }

                                return component.ref?.onKeyDown(props);
                            },

                            onExit() {
                                popup[0].destroy();
                                component.destroy();
                            },
                        };
                    },
                },
            }),
        ],
        editorProps: {
            attributes: {
                class: 'prose prose-sm dark:prose-invert max-w-none focus:outline-none min-h-[42px] max-h-[120px] overflow-y-auto px-3 py-2 text-sm leading-normal text-gray-800 dark:text-gray-200',
            },
            handleKeyDown: (view, event) => {
                // Don't intercept Enter when mention suggestion is open
                if (event.key === 'Enter' && !event.shiftKey) {
                    // Check if there's an active suggestion
                    const mentionPlugin = view.state.plugins.find(
                        (plugin) => plugin.key && plugin.key.includes && plugin.key.includes('mention'),
                    );
                    if (mentionPlugin) {
                        const pluginState = mentionPlugin.getState(view.state);
                        if (pluginState?.active) {
                            return false; // Let the mention plugin handle it
                        }
                    }

                    event.preventDefault();
                    handleSend();
                    return true;
                }
                return false;
            },
        },
        onUpdate: () => {
            emit('typing');
        },
    });

    async function handleSend() {
        if (!editor.value || isSending.value || props.disabled) return;

        const rawContent = editor.value.getJSON();
        const content = cleanTipTapContent(JSON.parse(JSON.stringify(rawContent)));
        const isEmpty = editor.value.isEmpty;

        if (isEmpty) return;

        isSending.value = true;
        try {
            await emit('send', content);
            editor.value.commands.clearContent();
        } finally {
            isSending.value = false;
        }
    }

    watch(
        () => props.placeholder,
        (newPlaceholder) => {
            if (editor.value) {
                editor.value.extensionManager.extensions.find((ext) => ext.name === 'placeholder')?.options &&
                    (editor.value.extensionManager.extensions.find(
                        (ext) => ext.name === 'placeholder',
                    ).options.placeholder = newPlaceholder);
            }
        },
    );

    onBeforeUnmount(() => {
        editor.value?.destroy();
    });
</script>

<template>
    <div class="border-t border-gray-200/80 dark:border-gray-700/80 bg-white dark:bg-gray-900 p-4 shadow-lg shrink-0">
        <div class="flex items-end gap-2">
            <!-- Editor -->
            <div
                class="flex-1 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 overflow-hidden focus-within:ring-2 focus-within:ring-primary-500 focus-within:border-transparent transition-shadow"
            >
                <EditorContent :editor="editor" :class="[disabled ? 'opacity-50' : '']" />
            </div>

            <!-- Send Button -->
            <button
                type="button"
                class="bg-primary-600 hover:bg-primary-700 active:bg-primary-800 text-white rounded-lg p-3 font-medium transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900 shadow-sm hover:shadow disabled:opacity-50 disabled:cursor-not-allowed shrink-0"
                :disabled="disabled || isSending"
                aria-label="Send message"
                @click="handleSend"
            >
                <ArrowPathIcon v-if="isSending" class="w-5 h-5 animate-spin" />
                <PaperAirplaneIcon v-else class="w-5 h-5" />
            </button>
        </div>

        <p class="mt-2 text-xs text-gray-500 dark:text-gray-500">
            Press Enter to send, Shift+Enter for new line. Type @ to mention someone.
        </p>
    </div>
</template>

<!-- Placeholder styles require CSS pseudo-elements which cannot be done with Tailwind classes -->
<style>
    .ProseMirror p.is-editor-empty:first-child::before {
        @apply text-gray-400 dark:text-gray-500 pointer-events-none;
        content: attr(data-placeholder);
        position: absolute;
        top: 1px;
        left: 0;
    }

    .ProseMirror p.is-editor-empty:first-child {
        position: relative;
    }

    .ProseMirror p {
        @apply m-0;
    }
</style>
