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
    import { computed, useSlots } from 'vue';

    const props = defineProps({
        modelValue: { type: String, default: '' },
        type: { type: String, default: 'text' },
        placeholder: { type: String, default: '' },
        disabled: { type: Boolean, default: false },
        label: { type: String, default: '' },
    });

    const emit = defineEmits(['update:modelValue']);

    const slots = useSlots();

    const hasLeadingIcon = computed(() => !!slots['leading-icon']);

    function handleInput(event) {
        emit('update:modelValue', event.target.value);
    }
</script>

<template>
    <div>
        <label v-if="label" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ label }}
        </label>
        <div class="relative">
            <div v-if="hasLeadingIcon" class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                <slot name="leading-icon" />
            </div>
            <input
                :type="type"
                :value="modelValue"
                :placeholder="placeholder"
                :disabled="disabled"
                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 py-2.5 text-sm text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500 disabled:cursor-not-allowed disabled:opacity-50"
                :class="[hasLeadingIcon ? 'pl-10 pr-4' : 'px-4']"
                @input="handleInput"
            />
        </div>
    </div>
</template>
