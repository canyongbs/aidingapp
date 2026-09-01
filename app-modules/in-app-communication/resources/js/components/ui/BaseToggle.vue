<!--
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Aiding App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Aiding App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    <https://www.canyongbs.com> or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
-->

<script setup>
    const props = defineProps({
        modelValue: { type: Boolean, default: false },
        label: { type: String, default: '' },
        description: { type: String, default: '' },
        disabled: { type: Boolean, default: false },
    });

    const emit = defineEmits(['update:modelValue']);

    function toggle() {
        if (props.disabled) return;
        emit('update:modelValue', !props.modelValue);
    }
</script>

<template>
    <div class="flex items-center justify-between gap-4">
        <div v-if="label" class="min-w-0">
            <span class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ label }}</span>
            <span v-if="description" class="mt-0.5 block text-xs text-gray-500 dark:text-gray-400">
                {{ description }}
            </span>
        </div>

        <button
            type="button"
            role="switch"
            :aria-checked="modelValue"
            :aria-label="label || 'Toggle'"
            :disabled="disabled"
            class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900 disabled:cursor-not-allowed disabled:opacity-50"
            :class="[modelValue ? 'bg-primary-600' : 'bg-gray-200 dark:bg-gray-700']"
            @click="toggle"
        >
            <span
                aria-hidden="true"
                class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200"
                :class="[modelValue ? 'translate-x-5' : 'translate-x-0']"
            />
        </button>
    </div>
</template>
