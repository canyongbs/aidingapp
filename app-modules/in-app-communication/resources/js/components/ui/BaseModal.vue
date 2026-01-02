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
    import { XMarkIcon } from '@heroicons/vue/24/outline';
    import { onMounted, onUnmounted, useSlots, watch } from 'vue';

    const props = defineProps({
        isOpen: { type: Boolean, default: false },
        title: { type: String, required: true },
        maxHeight: { type: Boolean, default: false },
        noPadding: { type: Boolean, default: false },
    });

    const emit = defineEmits(['close']);

    const slots = useSlots();

    function handleClose() {
        emit('close');
    }

    function handleKeydown(event) {
        if (event.key === 'Escape' && props.isOpen) {
            handleClose();
        }
    }

    watch(
        () => props.isOpen,
        (isOpen) => {
            if (isOpen) {
                document.addEventListener('keydown', handleKeydown);
            } else {
                document.removeEventListener('keydown', handleKeydown);
            }
        },
    );

    onMounted(() => {
        if (props.isOpen) {
            document.addEventListener('keydown', handleKeydown);
        }
    });

    onUnmounted(() => {
        document.removeEventListener('keydown', handleKeydown);
    });
</script>

<template>
    <teleport to="body">
        <div v-if="isOpen" class="fixed inset-0 z-50">
            <!-- Backdrop -->
            <transition
                enter-active-class="transition-opacity duration-300"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition-opacity duration-300"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
                appear
            >
                <div class="fixed inset-0 bg-gray-950/50 dark:bg-gray-950/75" aria-hidden="true" @click="handleClose" />
            </transition>

            <!-- Modal Container -->
            <div class="fixed inset-0 overflow-y-auto p-4 cursor-pointer" @click="handleClose">
                <div class="grid min-h-full grid-rows-[1fr_auto_1fr] justify-items-center sm:grid-rows-[1fr_auto_3fr]">
                    <transition
                        enter-active-class="transition-all duration-300"
                        enter-from-class="opacity-0 scale-95"
                        enter-to-class="opacity-100 scale-100"
                        leave-active-class="transition-all duration-300"
                        leave-from-class="opacity-100 scale-100"
                        leave-to-class="opacity-0 scale-95"
                        appear
                    >
                        <div
                            class="row-start-2 w-full max-w-md rounded-xl bg-white dark:bg-gray-900 shadow-xl ring-1 ring-gray-950/5 dark:ring-white/10 cursor-default"
                            :class="[maxHeight ? 'flex flex-col max-h-[80vh]' : '']"
                            @click.stop
                        >
                            <!-- Header -->
                            <div class="flex items-center justify-between px-6 pt-6 shrink-0">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="flex items-center justify-center rounded-full bg-primary-100 dark:bg-primary-500/20 p-2"
                                    >
                                        <slot name="icon" />
                                    </div>
                                    <h2 class="text-base font-semibold text-gray-950 dark:text-white">{{ title }}</h2>
                                </div>
                                <button
                                    type="button"
                                    class="text-gray-400 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors rounded-lg p-1.5 -m-1.5"
                                    @click="handleClose"
                                >
                                    <XMarkIcon class="w-5 h-5" />
                                </button>
                            </div>

                            <!-- Body -->
                            <div :class="[noPadding ? '' : 'px-6 py-6', maxHeight ? 'flex-1 overflow-y-auto' : '']">
                                <slot />
                            </div>

                            <!-- Footer -->
                            <div v-if="slots.footer" class="flex justify-end gap-3 px-6 pb-6 shrink-0">
                                <slot name="footer" />
                            </div>
                        </div>
                    </transition>
                </div>
            </div>
        </div>
    </teleport>
</template>
