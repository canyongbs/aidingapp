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
    import { ArrowRightIcon } from '@heroicons/vue/20/solid';

    defineProps({
        selectedType: { type: Object, required: true },
        modelValue: { type: String, required: true },
    });

    defineEmits(['update:modelValue', 'continue']);
</script>

<template>
    <div class="shrink-0 border-t border-gray-100 bg-gray-50 px-4 pt-3 pb-4">
        <div class="flex items-center gap-1.5 mb-2">
            <span class="text-xs font-medium text-gray-500">Selected</span>
            <span class="text-xs text-gray-700 font-medium truncate">{{ selectedType.name }}</span>
        </div>
        <div class="flex items-center gap-2">
            <div class="flex-1 relative">
                <select
                    :value="modelValue"
                    @change="$emit('update:modelValue', $event.target.value)"
                    :disabled="!selectedType.priorities?.length"
                    class="w-full h-9 appearance-none bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-700 pr-8 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent disabled:opacity-40 disabled:cursor-not-allowed transition-all cursor-pointer"
                >
                    <option value="" disabled>
                        {{ selectedType.priorities?.length ? 'Select priority…' : 'No priorities available' }}
                    </option>
                    <option v-for="priority in selectedType.priorities" :key="priority.id" :value="priority.id">
                        {{ priority.name }}
                    </option>
                </select>
                <svg
                    class="pointer-events-none absolute right-2.5 inset-y-0 my-auto w-4 h-4 text-gray-400"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </div>

            <button
                @click="$emit('continue')"
                :disabled="!modelValue"
                :class="[
                    'shrink-0 h-9 flex items-center gap-1.5 px-4 rounded-lg text-sm font-medium transition-all',
                    modelValue
                        ? 'bg-brand-500 hover:bg-brand-600 text-white shadow-sm'
                        : 'bg-gray-100 text-gray-300 cursor-not-allowed',
                ]"
            >
                Continue
                <ArrowRightIcon class="w-4 h-4" />
            </button>
        </div>
    </div>
</template>
