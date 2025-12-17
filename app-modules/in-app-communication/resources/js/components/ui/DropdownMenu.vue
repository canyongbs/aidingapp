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
    import { computed, onMounted, onUnmounted, ref } from 'vue';

    const props = defineProps({
        title: { type: String, default: null },
        align: { type: String, default: 'right' },
        width: { type: String, default: 'w-64' },
    });

    const isOpen = ref(false);
    const containerRef = ref(null);

    function toggle() {
        isOpen.value = !isOpen.value;
    }

    function close() {
        isOpen.value = false;
    }

    function handleClickOutside(event) {
        if (containerRef.value && !containerRef.value.contains(event.target)) {
            close();
        }
    }

    onMounted(() => {
        document.addEventListener('click', handleClickOutside);
    });

    onUnmounted(() => {
        document.removeEventListener('click', handleClickOutside);
    });

    const alignmentClass = computed(() => {
        return props.align === 'left' ? 'left-0' : 'right-0';
    });

    defineExpose({ toggle, close, isOpen });
</script>

<template>
    <div ref="containerRef" class="relative">
        <slot name="trigger" :toggle="toggle" :is-open="isOpen" />

        <transition
            enter-active-class="transition ease-out duration-100"
            enter-from-class="transform opacity-0 scale-95"
            enter-to-class="transform opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="transform opacity-100 scale-100"
            leave-to-class="transform opacity-0 scale-95"
        >
            <div
                v-if="isOpen"
                class="absolute top-full mt-2 rounded-lg bg-white dark:bg-gray-800 shadow-lg ring-1 ring-black/5 dark:ring-white/10 z-50"
                :class="[alignmentClass, width]"
            >
                <div class="p-2">
                    <p v-if="title" class="px-3 py-2 text-xs font-medium text-gray-500 dark:text-gray-400">
                        {{ title }}
                    </p>
                    <slot :close="close" />
                </div>
            </div>
        </transition>
    </div>
</template>
