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
    import { ChevronDownIcon } from '@heroicons/vue/16/solid';

    defineProps({
        modelValue: {
            type: String,
            default: null,
        },
        pipelines: {
            type: Array,
            required: true,
        },
    });

    defineEmits(['update:modelValue']);
</script>

<template>
    <div class="relative inline-block w-full max-w-xs">
        <select
            :value="modelValue"
            aria-label="Select pipeline"
            class="w-full appearance-none rounded-[var(--rounding-md)] border border-gray-300 bg-white py-2 pl-3 pr-9 text-sm text-gray-900 focus:border-[rgb(var(--primary-500))] focus:outline-hidden focus:ring-2 focus:ring-[rgb(var(--primary-500))]"
            @change="$emit('update:modelValue', $event.target.value)"
        >
            <option v-for="pipeline in pipelines" :key="pipeline.id" :value="pipeline.id">
                {{ pipeline.name }}
            </option>
        </select>
        <ChevronDownIcon
            class="pointer-events-none absolute right-2.5 top-1/2 size-4 -translate-y-1/2 text-gray-400"
            aria-hidden="true"
        />
    </div>
</template>
